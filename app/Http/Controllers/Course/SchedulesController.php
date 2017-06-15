<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\BaseController;

use Illuminate\Http\Request;
use App\Http\Requests\Course\ScheduleRequest;

use App\Course;
use App\Schedule;
use App\Repositories\Courses;
use App\Repositories\Schedules;
use App\Repositories\Weekdays;

use App\Support\Helper;
use App\Http\Middleware\CheckAdmin;


class SchedulesController extends BaseController
{
    public function __construct(Schedules $schedules , Courses $courses, 
                            Weekdays $weekdays, CheckAdmin $checkAdmin) 
    {
         $exceptAdmin=[];
         $allowVisitors=[];
         $this->setMiddleware( $exceptAdmin, $allowVisitors);
        

         $this->schedules=$schedules;  
         $this->courses=$courses;
         $this->weekdays=$weekdays; 

         $this->setCheckAdmin($checkAdmin);     
	}
    public function index()
    {
         $request = request();
         $course_id=(int)$request->course; 

         $scheduleList=$this->schedules->getByCourse($course_id)->get();
         if(count($scheduleList)){
            
            $current_user=$this->currentUser();
            foreach ($scheduleList as $schedule) {
                 $schedule->canEdit=$schedule->canEditBy($current_user);
                 $schedule->canDelete=$schedule->canDeleteBy($current_user);
            }
        }
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
    public function show($id)
    {
        $schedule=$this->schedules->findOrFail($id);
        return response()->json(['schedule' => $schedule]);  
       
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
    public function import(Request $request)
    {
         $from_course=$request['from_course'];  
         $to_course=$request['to_course'];  

         $current_user=$this->currentUser(); 
         $toCourse=Course::findOrFail($to_course);
         if(!$toCourse->canEditBy($current_user)){
             return  $this->unauthorized();  
         }

         $fromCourse=$this->courses->findOrFail($from_course);
         $updated_by=$current_user->id;
         $this->schedules->import($fromCourse,$toCourse,$updated_by);

         return response()->json([ 'saved' => true]);    
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
