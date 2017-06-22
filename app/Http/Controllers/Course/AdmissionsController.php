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
        // $course_id=1;
        // $signup_id=5;
        // $admission=Admission::findOrFail($course_id);
        //  $admit=new Admit();
        //     $admit->signup_id=$signup_id;
        //     $admit->updated_by=$current_user->id;

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
       
        $admitList=Admit::where('course_id',$id);
        if($admission){
            $admission->canEdit=$admission->canEditBy($current_user);
            $admission->canDelete=$admission->canDeleteBy($current_user);
           
        }else{
            $course->canCreateAdmit=$course->canCreateAdmit();           
        }

        $admitList=$admitList->with(['signup.user.profile'])->filterPaginateOrder();

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

        $admission= new Admission();
        $admission->updated_by=$current_user->id;
        $course->admission()->save($admission);
       
        for($i = 0; $i < $rows; ++$i) {
            $admit=new Admit();
            $admit->course_id=$course_id;
            $admit->signup_id=$selected_signups[$i];
            $admit->updated_by=$current_user->id;
            $admit->save();
           
        }
        
        return response() ->json([ 'admission' => $admission ]);
    }
    public function edit($id)
    {
        $current_user=$this->currentUser();
        $admission=Admission::findOrFail($id);
        if($admission->hasRemoved()) {
            abort(404);
        }
        if(!$admission->canEditBy($current_user)){
            return  $this->unauthorized(); 
        }
        if($admission->signupStopped()) $admission->signup=0;
        else   $admission->signup=1;
        
        if($admission->classStopped()) $admission->class=0;
        else   $admission->class=1;
        
        return response()->json(['admission' => $admission ]);        
    }
    public function update(AdmissionRequest $request, $id)
    {
        $current_user=$this->currentUser();
        $admission=Admission::findOrFail($id);
        if($admission->hasRemoved()) {
            abort(404);
        }
        if(!$admission->canEditBy($current_user)){
            return  $this->unauthorized(); 
        }

        $updated_by=$current_user->id;
        $values=$request->getValues();

        $admission->ps=$values['ps'];
        $admission->updated_by=$updated_by;

        $signup=(int)$values['signup'];
        $class=(int)$values['class'];

        if($class==0){
            $admission->class=0;
            $admission->signup=0;           
        }else if($signup==0){
            $admission->signup=0;
        } 

        $admission->updateAdmission();

        return response()->json(['admission' => $admission ]);   
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
