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

use Carbon\Carbon;

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

    public function index()
    {
        $request=request();
        $lesson_id=(int)$request->lesson;
        if(!$lesson_id) abort(404);

        
        $user_id=(int)$request->user;

        $leaveList=Leave::where('lesson_id',$lesson_id);
        if($user_id){
            $leaveList=Leave::where('user_id',$user_id);
        }
        $current_user=$this->currentUser();
        $leaveList=$leaveList->with('user.profile')
                             ->orderBy('type_id')->get();
        if(count($leaveList)){
            foreach($leaveList as $leave)
            {
                $leave->typeName= $leave->typeName();
                $leave->canEdit= $leave->canEditBy($current_user);
                $leave->canDelete= $leave->canDeleteBy($current_user);
            }
        }
        
        return response()->json(['leaveList' => $leaveList]);           

    }

    public function  create()
    {
        $request=request();
        $lesson_id=(int)$request->lesson;

        $lesson=Lesson::findOrFail($lesson_id);
        $current_user=$this->currentUser();
        if(!$lesson->canEditBy($current_user)){
            return  $this->unauthorized();  
        }  
        $leave=Leave::initialize($lesson);
        $typeOptions=$this->leaves->options();

        $userOptions=$this->userOptions($lesson);
        
        return response()
                   ->json([ 
                            'leave' => $leave ,
                            'typeOptions' => $typeOptions ,
                            'userOptions' => $userOptions 
                          ]); 
    }
    public function store(LeaveRequest $request)
    {
        $current_user=$this->currentUser();
        $updated_by=$current_user->id;
        

        $lesson_id=$request->getLessonId();
        $lesson=Lesson::findOrFail($lesson_id);
        if(!$lesson->canEditBy($current_user)){
            return  $this->unauthorized();  
        }  
        $error=$request->check($lesson);
        if($error){
            return   response()->json($error ,  422); 
        }

        $values=$request->getValues($lesson,$updated_by);
       
        $leave=Leave::create($values);

         return response() ->json($leave);  
    }
    
    public function edit($id)
    {
        $current_user=$this->currentUser();
        $leave=Leave::findOrFail($id);
        
        if(!$leave->canEditBy($current_user)){
            return  $this->unauthorized(); 
        }
        $leave->begin_at=Helper::getTimeNumber($leave->begin_at);
        $leave->end_at=Helper::getTimeNumber($leave->end_at);
        $typeOptions=$this->leaves->options();
        $userOptions=$this->userOptions($leave->lesson);
        $selectedUser=$leave->user->toOption();
        
        return response()
                   ->json([ 
                            'leave' => $leave ,
                            'typeOptions' => $typeOptions ,
                            'userOptions' => $userOptions ,
                            'selectedUser' => $selectedUser
                          ]);         
    }
    public function update(LeaveRequest $request, $id)
    {
        $current_user=$this->currentUser();
        $leave=Leave::findOrFail($id);
        if(!$leave->canEditBy($current_user)){
            return  $this->unauthorized(); 
        }
        
        $lesson=Lesson::findOrFail($leave->lesson_id);
        $error=$request->check($lesson);
        if($error){
            return   response()->json($error ,  422); 
        }
        $updated_by=$current_user->id;
        $values=$request->getValues($lesson,$updated_by);
       
        $leave->update($values);

        return response() ->json($leave);  
    }
    public function destroy($id)
    {
        $leave=Leave::findOrFail($id);
        $current_user=$this->currentUser();
        if(!$leave->canDeleteBy($current_user)){
            return  $this->unauthorized();
        }   

        $leave->delete();

        return response()->json(['deleted' => true ]);     
    }


    public function options()
    {
        $options=$this->leaves->options();
        
        return response() ->json(['options' => $options ]);  
           
    }

    private function userOptions($lesson)
    {
        $students=$lesson->students()->with('user.profile')->get();
        $userOptions=[];
        foreach($students as $lessonParticipant)
        {
            $item=$lessonParticipant->toOption();
            array_push($userOptions,  $item);
        }

        return $userOptions;
    }
    
   
   
}
