<?php

namespace App\Http\Controllers\Api\Teachers;

use App\Http\Controllers\Api\Teachers\BaseController;

use Illuminate\Http\Request;
use App\Http\Requests\Course\ScheduleRequest;

use App\Course;
use App\Schedule;
use App\Repositories\Courses;
use App\Repositories\Schedules;
use App\Repositories\Teachers;

use App\Support\Helper;


class ImportSchedulesController extends BaseController
{
    public function __construct(Schedules $schedules , Courses $courses, Teachers $teachers)                               
    {
        parent::__construct();

        $this->schedules=$schedules;  
        $this->teachers=$teachers;  
        $this->courses=$courses;   
	}
    
    private function getSchedules($course_id)
    {
        $scheduleList=$this->schedules->getByCourse($course_id)->get();
        return response()->json(['scheduleList' => $scheduleList ]);
    }
    public function create()
    {
        $request = request();
        $course_id=(int)$request->course; 
        if($course_id) return $this->getSchedules($course_id);

        $to_course_id=(int)$request->to; 

        $current_user=$this->currentUser();
        $teacher_id=$current_user->id;
        
        $courseList=$this->courses->getByTeacher($teacher_id)
                                  ->where('id','!=', $to_course_id)
                                  ->whereHas('schedules')
                                  ->orderBy('begin_date','desc')
                                  ->get();
        
        $courseOptions=[];
        $scheduleList=[];
        if(count($courseList)){
            $courseOptions=$this->courses->optionsConverting($courseList); 
            $course_id=$courseOptions[0]['value'];  
            $scheduleList=$this->schedules->getByCourse($course_id)->get();                             
        }  
        
        $import=array(
            'from_course' => 0,
            'to_course' => 0,
            
        );

        

        return response()->json([   'import' => $import,
                                    'courseOptions' => $courseOptions ,
                                    'scheduleList' => $scheduleList          
                                ]);
                       
            
            
    }
    public function store(Request $request)
    {
         $values=$request['import']; 
         
         $from_course=$values['from_course'];  
         $to_course=$values['to_course'];  

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
  
    
   
}
