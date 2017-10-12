<?php

namespace App\Http\Controllers\Api\Teachers;

use App\Http\Controllers\Api\Teachers\BaseController;
use Illuminate\Http\Request;
use App\Course;
use App\Schedule;
use App\Repositories\Courses;
use App\Repositories\Schedules;

use App\Http\Requests\Course\ScheduleRequest;


use App\Support\Helper;
use DB;


class SchedulesController extends BaseController
{
   
    public function __construct(Courses $courses,Schedules $schedules)                     
    {
        parent::__construct();
        $this->courses=$courses;
        $this->schedules=$schedules;
    }

    public function index()
    {
        $request = request();
        $course_id=(int)$request->course; 

        $scheduleList=$this->schedules->getByCourse($course_id)->get();
        
        return response()->json(['scheduleList' => $scheduleList ]);
        

    }
    public function create()
    {
        $request = request();
        $course_id=(int)$request->course; 

        $course=Course::findOrFail($course_id);
        $current_user=$this->currentUser();
        if(!$course->canEditBy($current_user)){
            return  $this->unauthorized();  
        }

        $schedule=$this->schedules->initialize($course_id);

        return response()->json(['schedule' => $schedule]);  
            
    }
    public function store(ScheduleRequest $request)
    {
        $current_user=$this->currentUser();
        $removed=false;
        $updated_by=$current_user->id;
        $values=$request->getValues($updated_by,$removed);

        $course_id=$values['course_id']; 
        $course=Course::findOrFail($course_id);
        if(!$course->canEditBy($current_user)){
             return  $this->unauthorized();      
        }
       
        $schedule = Schedule::create($values);
       
        return response()->json($schedule);
      
    }
    public function edit($id)
    {
       
        $schedule=$this->schedules->findOrFail($id);  
        $current_user=$this->currentUser();
        
        if(!$schedule->canEditBy($current_user)){
             return  $this->unauthorized();
        }
        return response()->json(['schedule' => $schedule]);    
    }
    public function update(ScheduleRequest $request, $id)
    {
         $schedule=$this->schedules->findOrFail($id);  
         $current_user=$this->currentUser();
        
         if(!$schedule->canEditBy($current_user)){
            return  $this->unauthorized(); 
         }
         $removed=false;
         $updated_by=$current_user->id;
         $values=$request->getValues($updated_by,$removed); 

         $schedule->update($values);

         return response()->json($schedule);
    }
    public function destroy($id)
    {
        $schedule=$this->schedules->findOrFail($id);  
        $current_user=$this->currentUser();       
        if(!$schedule->canDeleteBy($current_user)){
            return  $this->unauthorized();  
        }      
        $schedule->delete();
        return response() ->json(['deleted' => true ]);
       
    }
}
