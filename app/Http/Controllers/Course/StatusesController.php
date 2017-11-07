<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

use App\Http\Requests\Course\StatusRequest;

use App\Repositories\Courses;
use App\Repositories\Terms;
use App\Repositories\Centers;
use App\Status;
use App\Course;

use App\Support\Helper;

class StatusesController extends BaseController
{
    
    public function __construct(Courses $courses,Terms $terms , Centers $centers) 
    {
        $this->courses=$courses;
        $this->terms=$terms;
        $this->centers=$centers;

	}
    public function index()
    {
        if(!request()->ajax()){
            $menus=$this->menus('courses');            
            return view('courses.statuses')
                    ->with(['menus' => $menus]);
        }   

        $courseList=$this->courses->getAll();

        $request = request();
        $termId=(int)$request->term;     
        if($termId){
            $courseList=$courseList->where('term_id',$termId);
        }

        $centerId=(int)$request->center;
        if($centerId){
            $courseList=$courseList->where('center_id',$centerId);
        }
        
        $courseList=$courseList->with('status')->filterPaginateOrder();
        return response() ->json(['model' => $courseList  ]);
    }
    
    public function show($id)
    {
        $current_user=$this->currentUser();
        $status=Status::with('course')->findOrFail($id);
        if($status->hasRemoved()) {
            abort(404);
        }
        $status->canEdit=$status->canEditBy($current_user);

        return response()->json(['status' => $status ]);   
       
    }
    public function edit($id)
    {
        $current_user=$this->currentUser();
        $status=Status::with('course')->findOrFail($id);
        if($status->hasRemoved()) {
            abort(404);
        }
        if(!$status->canEditBy($current_user)){
            return  $this->unauthorized(); 
        }

        // if($status->signupStopped()) $status->signup=0;
        // else   $status->signup=1;
        
        // if($status->classStopped()) $status->class=0;
        // else   $status->class=1;
        
        return response()->json(['status' => $status ]);        
    }
    public function update(StatusRequest $request, $id)
    {
        $current_user=$this->currentUser();
        $status=Status::with('course')->findOrFail($id);
        if($status->hasRemoved()) {
            abort(404);
        }
        if(!$status->canEditBy($current_user)){
            return  $this->unauthorized(); 
        }

        $updated_by=$current_user->id;

        $active=$request->getActive();

        $status->course->active=$active;
        $status->course->updated_by=$updated_by;
        $status->course->save();

       
       
        
        $values=$request->getValues();

        $status->ps=$values['ps'];
        $status->updated_by=$updated_by;

        // $signup=(int)$values['signup'];
        // $class=(int)$values['class'];
        
        // $status->class = $class;
        // $status->signup=$signup;

        
        // if($class==0) $status->signup=0; //已停止

        $status->updateStatus();

        return response()->json(['status' => $status ]);   
    }
    
   
   
}
