<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

use App\Repositories\Courses;
use App\Repositories\Signups;

use App\Register;
use App\Course;
use App\Signup;
use App\Student;

use App\Support\Helper;
use App\Http\Middleware\CheckAdmin;

use DB;


class RegistersController extends BaseController
{
    protected $key='registers';
    public function __construct(Courses $courses,Signups $signups,CheckAdmin $checkAdmin) 
    {
       
		$exceptAdmin=[];
        $allowVisitors=[];
        $this->setMiddleware( $exceptAdmin, $allowVisitors);
        
        $this->courses=$courses;
        $this->signups=$signups;

        $this->setCheckAdmin($checkAdmin);
		
	}

    public function index()
    {
        $menus=$this->menus($this->key);            
            return view('registers.index')
                    ->with(['menus' => $menus]); 
    }
    
    public function show($id)
    {
        $current_user=$this->currentUser();
        $course=Course::findOrFail($id);
        $course->status;
        $register=$course->register;
        
        if($register){
            $register->canEdit=$register->canEditBy($current_user);
        }else{
            $course->canCreateRegister=$course->canCreateRegister();           
        }

        $studentList=Student::where('course_id',$id) ->with(['user.profile']);

        $studentList=$studentList->filterPaginateOrder();

        return response() ->json([ 'model' => $studentList,
                                   'course' => $course,
                                 
                                 ]); 
       
    }
    public function create()
    {
        $request = request();
        $course_id=(int)$request->course; 
        $course= $this->courses->findOrFail($course_id);

        if(!$course->canCreateRegister()) abort(404);

        $current_user=$this->currentUser();
        if(!$course->canEditBy($current_user)){
            return  $this->unauthorized();  
        }  

        //已繳費的錄取者
        $validAdmits=$course->admission->validAdmits()->pluck('signup_id');

        $signupList=Signup::whereIn('id',$validAdmits)->orderBy('date','desc')
                                            ->with('user.profile')
                                            ->get();  
        
       
        $studentList=[];
        $selected=[];
        for($i = 0; $i < count($signupList); ++$i) {
            $signup=$signupList[$i];
            $student=new Student();
            $student->user_id=$signup->user_id;
            $student->signup=$signup;
            array_push($studentList,  $student);

            array_push($selected,  $signup->user_id);
        }
        return response() ->json([ 'studentList' => $studentList,
                                    'selected' => $selected,
                                    'course' => $course
                                 ]); 
    }
    public function store(Request $request)
    {
        $course_id=$request->course_id;
        $current_user=$this->currentUser();
        $course= $this->courses->findOrFail($course_id);

        if(!$course->canCreateRegister()) abort(404);

        $current_user=$this->currentUser();
        if(!$course->canEditBy($current_user)){
            return  $this->unauthorized();  
        }  

        $selected_users=$request->selected;
        $rows=count($selected_users);
        if(!$rows) abort(404);

        $studentList=[];
        for($i = 0; $i < $rows; ++$i) {
            $student=new Student();
            $student->number=$i+1;
            $student->join_date=$course->begin_date;
            $student->course_id=$course_id;
            $student->user_id=$selected_users[$i];
            $student->updated_by=$current_user->id;
            
            array_push($studentList,  $student);
        }


        $register= DB::transaction(function() 
            use($studentList,$course,$current_user){
                $register= new Register();
                $register->updated_by=$current_user->id;
                $course->register()->save($register);

                $register=Register::find($course->id);
                $register->students()->saveMany($studentList);

                return $register;
              
        });
        
        return response() ->json($register);
    }
    public function edit($id)
    {
        $current_user=$this->currentUser();
        $course= $this->courses->findOrFail($id);

        if(!$course->register) abort(404);
      
        if(!$course->register->canEditBy($current_user)){
            return  $this->unauthorized(); 
        }

        $except_signups=$course->register->students->pluck('signup_id');

        $signupList=$course->validSignups()->whereNotIn('id',$except_signups)
                                            ->where('status','>',-1)
                                            ->orderBy('status','desc')
                                            ->orderBy('date','desc')
                                            ->with('user.profile')
                                            ->get();  
        
       
        $studentList=[];
      
        for($i = 0; $i < count($signupList); ++$i) {
            $signup=$signupList[$i];
            $student=new Student();
            $student->signup_id=$signup->id;
            $student->signup=$signup;
            array_push($studentList,  $student);
            
        }
        return response() ->json([ 'studentList' => $studentList,
                                    'course' => $course
                                 ]); 
        
        
        return response()->json(['register' => $register ]);        
    }
    public function update(Request $request, $id)
    {
        $current_user=$this->currentUser();
        $register=Register::findOrFail($id);
       
        if(!$register->canEditBy($current_user)){
            return  $this->unauthorized(); 
        }

        $selected_signups=$request->selected;
        $rows=count($selected_signups);
        if(!$rows) abort(404);

        $course_id=$id;
        $studentList=[];
        for($i = 0; $i < $rows; ++$i) {
            $student=new Student();
            $student->course_id=$course_id;
            $student->signup_id=$selected_signups[$i];
            $student->join_date=$course->begin_date;
            $student->updated_by=$current_user->id;
            
            array_push($studentList,  $student);
        }

        $register->students()->saveMany($studentList);
        
        return response() ->json($register);   
    }

    public function destroy($id)
    {
        $student=Student::findOrFail($id); 
        $register=Register::findOrFail($student->course_id);
        $current_user=$this->currentUser();

        if(!$student->canDeleteBy($current_user)){
            return  $this->unauthorized();
        }    

        $student->delete();

        if(!count($register->students)){
             $register->delete();
        }

        return response()->json(['deleted' => true ]);     
    }


    private function getStudentSummary($course)
    {
        $signup_ids=Student::where('course_id',$course->id)->get()->pluck('signup_id');
        $info = DB::table('signups')
                        ->whereIn('id',$signup_ids)
                        ->select('status', DB::raw('count(*) as total'))
                        ->groupBy('status')->get();
        return $this->signups->printSignupSummary($info); 
    }
    
   
   
}
