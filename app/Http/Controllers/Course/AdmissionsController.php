<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

use App\Repositories\Courses;
use App\Repositories\Signups;

use App\Admission;
use App\Course;
use App\Admit;

use App\Support\Helper;
use App\Http\Middleware\CheckAdmin;

use DB;


class AdmissionsController extends BaseController
{
    protected $key='admissions';
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
            return view('admissions.index')
                    ->with(['menus' => $menus]); 
    }
    
    public function show($id)
    {
        $current_user=$this->currentUser();
        $course=Course::findOrFail($id);
        $course->status;
        $admission=$course->admission;
        
        if($admission){
            $admission->canEdit=$admission->canEditBy($current_user);
        }else{
            $course->canCreateAdmit=$course->canCreateAdmit();           
        }

        $admitList=Admit::where('course_id',$id)->with(['signup.user.profile']);
        $request = request();
        $status=(int)$request->status;
        if( $status >= -1  && $status <=1 ){
             $admitList=$admitList->whereHas('signup', function($query)
                use ($status) {
                $query->where('status',$status);
            });
        }

        $admitList=$admitList->filterPaginateOrder();

        $signup_ids=Admit::where('course_id',$id)->get()->pluck('signup_id');
        $info = DB::table('signups')
                        ->whereIn('id',$signup_ids)
                        ->select('status', DB::raw('count(*) as total'))
                        ->groupBy('status')->get();

        $summary=$this->getAdmitSummary($course);

        return response() ->json([ 'model' => $admitList,
                                   'course' => $course,
                                   'summary' => $summary
                                 ]); 
       
    }
    public function create()
    {
        $request = request();
       
        $course_id=(int)$request->course; 
       
        $course= $this->courses->findOrFail($course_id);

        if(!$course->canCreateAdmit()) abort(404);

        $current_user=$this->currentUser();
        if(!$course->canEditBy($current_user)){
            return  $this->unauthorized();  
        }  

        $signupList=$course->validSignups()->where('status','>',-1)
                                            ->orderBy('status','desc')
                                            ->orderBy('date','desc')
                                            ->with('user.profile')
                                            ->get();  
        
        $limit=$course->limit;
        $admitList=[];
        $selected=[];
        for($i = 0; $i < count($signupList); ++$i) {
            $signup=$signupList[$i];
            $admit=new Admit();
            $admit->signup_id=$signup->id;
            $admit->signup=$signup;
            array_push($admitList,  $admit);

            if($i <= $limit){
                array_push($selected,  $signup->id);
            }
        }
        return response() ->json([ 'admitList' => $admitList,
                                    'selected' => $selected,
                                    'course' => $course
                                 ]); 
    }
    public function store(Request $request)
    {
        $course_id=$request->course_id;
        $current_user=$this->currentUser();
        $course= $this->courses->findOrFail($course_id);

        if(!$course->canCreateAdmit()) abort(404);

        $current_user=$this->currentUser();
        if(!$course->canEditBy($current_user)){
            return  $this->unauthorized();  
        }  

        $selected_signups=$request->selected;
        $rows=count($selected_signups);
        if(!$rows) abort(404);

        $admitList=[];
        for($i = 0; $i < $rows; ++$i) {
            $admit=new Admit();
            $admit->course_id=$course_id;
            $admit->signup_id=$selected_signups[$i];
            $admit->updated_by=$current_user->id;
            
            array_push($admitList,  $admit);
        }


        $admission= DB::transaction(function() 
            use($admitList,$course,$current_user){
                $admission= new Admission();
                $admission->updated_by=$current_user->id;
                $course->admission()->save($admission);

                $admission=Admission::find($course->id);
                $admission->admits()->saveMany($admitList);

                return $admission;
              
        });
        
        return response() ->json([ 'admission' => $admission ]);
    }
    public function edit($id)
    {
        $current_user=$this->currentUser();
        $course= $this->courses->findOrFail($id);

        if(!$course->admission) abort(404);
      
        if(!$course->admission->canEditBy($current_user)){
            return  $this->unauthorized(); 
        }

        $except_signups=$course->admission->admits->pluck('signup_id');

        $signupList=$course->validSignups()->whereNotIn('id',$except_signups)
                                            ->where('status','>',-1)
                                            ->orderBy('status','desc')
                                            ->orderBy('date','desc')
                                            ->with('user.profile')
                                            ->get();  
        
       
        $admitList=[];
      
        for($i = 0; $i < count($signupList); ++$i) {
            $signup=$signupList[$i];
            $admit=new Admit();
            $admit->signup_id=$signup->id;
            $admit->signup=$signup;
            array_push($admitList,  $admit);
            
        }
        return response() ->json([ 'admitList' => $admitList,
                                    'course' => $course
                                 ]); 
        
        
        return response()->json(['admission' => $admission ]);        
    }
    public function update(Request $request, $id)
    {
        $current_user=$this->currentUser();
        $admission=Admission::findOrFail($id);
       
        if(!$admission->canEditBy($current_user)){
            return  $this->unauthorized(); 
        }

        $selected_signups=$request->selected;
        $rows=count($selected_signups);
        if(!$rows) abort(404);

        $course_id=$id;
        $admitList=[];
        for($i = 0; $i < $rows; ++$i) {
            $admit=new Admit();
            $admit->course_id=$course_id;
            $admit->signup_id=$selected_signups[$i];
            $admit->updated_by=$current_user->id;
            
            array_push($admitList,  $admit);
        }

        $admission->admits()->saveMany($admitList);
        
        return response() ->json([ 'admission' => $admission ]);   
    }

    public function destroy($id)
    {
        $admit=Admit::findOrFail($id); 
        $admission=Admission::findOrFail($admit->course_id);
        $current_user=$this->currentUser();

        if(!$admit->canDeleteBy($current_user)){
            return  $this->unauthorized();
        }    

        $admit->delete();

        if(!count($admission->admits)){
             $admission->delete();
        }

        return response()->json(['deleted' => true ]);     
    }


    private function getAdmitSummary($course)
    {
        $signup_ids=Admit::where('course_id',$course->id)->get()->pluck('signup_id');
        $info = DB::table('signups')
                        ->whereIn('id',$signup_ids)
                        ->select('status', DB::raw('count(*) as total'))
                        ->groupBy('status')->get();
        return $this->signups->printSignupSummary($info); 
    }
    
   
   
}
