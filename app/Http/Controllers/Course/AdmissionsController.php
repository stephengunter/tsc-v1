<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

use App\Http\Requests\Course\AdmissionRequest;

use App\Repositories\Courses;

use App\Admission;
use App\Course;
use App\Admit;

use App\Support\Helper;
use App\Http\Middleware\CheckAdmin;

class AdmissionsController extends BaseController
{
    protected $key='admissions';
    public function __construct(Courses $courses,CheckAdmin $checkAdmin) 
    {
       
		$exceptAdmin=[];
        $allowVisitors=[];
        $this->setMiddleware( $exceptAdmin, $allowVisitors);
        
        $this->courses=$courses;

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
        $admitList=Admit::where('course_id',$id);
        if($admission){
            $admission->canEdit=$admission->canEditBy($current_user);
            $admission->canDelete=$admission->canDeleteBy($current_user);
            $admitList=$admission->admits;
           
        }else{
            $course->canCreateAdmit=$course->canCreateAdmit();           
        }

        $admitList=$admitList->with(['signup.user.profile'])->filterPaginateOrder();

        return response() ->json([ 'model' => $admitList,
                                       'course' => $course
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
        
        $admitList=[];
        
        foreach ($signupList as $signup) {
             $admit=new Admit();
             $admit->signup=$signup;
             array_push($admitList,  $admit);
        }

                                                                     
        $course->status;

        return response() ->json([ 'admitList' => $admitList,
                                       'course' => $course
                                 ]); 
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
    
   
   
}
