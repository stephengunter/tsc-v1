<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

use App\Http\Requests\Course\LeaveRequest;

use App\Leave;
use App\LeaveType;
use App\Lesson;

use App\Repositories\Leaves;

use App\Support\Helper;
use App\Http\Middleware\CheckAdmin;

class LeavesController extends BaseController
{
   
    public function __construct(Leaves $leaves,CheckAdmin $checkAdmin) 
    {
       
		$exceptAdmin=[];
        $allowVisitors=[];
        $this->setMiddleware( $exceptAdmin, $allowVisitors);
         
        $this->leaves=$leaves; 
        $this->setCheckAdmin($checkAdmin);
		
	}

    public function  create()
    {
        $request=request();
        $lesson_id=(int)$request->lesson;
        $lesson=Lesson::findOrFail($lesson_id);
        $leave=Leave::initialize();
        $typeOptions=$this->leaves->options();

        $students=$lesson->students()->with('user.profile')->get();
        $userOptions=[];
        foreach($students as $lessonParticipant)
        {
            $item=$lessonParticipant->toOption();
            array_push($userOptions,  $item);
        }
        return response()
                   ->json([ 
                            'leave' => $leave ,
                            'typeOptions' => $typeOptions ,
                            'userOptions' => $userOptions 
                          ]); 
    }
    
    public function show($id)
    {
        $current_user=$this->currentUser();
        $leave=Leave::findOrFail($id);
        if($leave->hasRemoved()) {
            abort(404);
        }
        $leave->canEdit=$leave->canEditBy($current_user);
        $leave->canDelete=$leave->canDeleteBy($current_user);

        return response()->json(['leave' => $leave ]);   
       
    }
    public function edit($id)
    {
        $current_user=$this->currentUser();
        $leave=Leave::findOrFail($id);
        if($leave->hasRemoved()) {
            abort(404);
        }
        if(!$leave->canEditBy($current_user)){
            return  $this->unauthorized(); 
        }
        if($leave->signupStopped()) $leave->signup=0;
        else   $leave->signup=1;
        
        if($leave->classStopped()) $leave->class=0;
        else   $leave->class=1;
        
        return response()->json(['leave' => $leave ]);        
    }
    public function update(LeaveRequest $request, $id)
    {
        $current_user=$this->currentUser();
        $leave=Leave::findOrFail($id);
        if($leave->hasRemoved()) {
            abort(404);
        }
        if(!$leave->canEditBy($current_user)){
            return  $this->unauthorized(); 
        }

        $updated_by=$current_user->id;
        $values=$request->getValues();

        $leave->ps=$values['ps'];
        $leave->updated_by=$updated_by;

        $signup=(int)$values['signup'];
        $class=(int)$values['class'];
        
        $leave->class = $class;
        $leave->signup=$signup;

        
        if($class==0) $leave->signup=0; 

        $leave->updateLeave();

        return response()->json(['leave' => $leave ]);   
    }

    public function options()
    {
        $options=$this->leaves->options();
        
        return response() ->json(['options' => $options ]);
           
                
           
    }
    
   
   
}
