<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\BaseController;

use Illuminate\Http\Request;

use App\Course;
use App\Schedule;
use App\Repositories\Courses;
use App\Repositories\Schedules;
use App\Repositories\Teachers;
use App\Repositories\Weekdays;

use App\Support\Helper;


use Illuminate\Support\Facades\Input;


class SchedulesImportController extends BaseController
{
    public function __construct(Schedules $schedules , Courses $courses, Teachers $teachers)                               
    {
        
         $this->schedules=$schedules;  
         $this->teachers=$teachers;  
         $this->courses=$courses;

    }
    
   
    public function create()
    {
        $course_id=0;
        $teacher_id=0;
        $teacherOptions=[];
        $courseOptions=[];
        $import=array(
            'from_course' => 0,
            'to_course' => 0,
            'teacher_id' => 0
        );

        $request = request();
        $course_id=(int)$request->course; 
        $teacher_id=(int)$request->teacher; 

        if($course_id){
            $import['to_course']=$course_id;
            $course=Course::findOrFail($course_id);
            $teachers=$course->teachers;
            $teacherOptions=$this->teachers->optionsConverting($teachers);

            $teacher_id=$teacherOptions[0]['value'];
        }

        if($teacher_id){
            $import['teacher_id'] = $teacher_id;
            $courseList=$this->courses->getByTeacher($teacher_id)->get();
                                     
            if(count($courseList)){
                 $courseOptions=$this->courses->optionsConverting($courseList);
                
            }  

            return response()
            ->json([
                'import' => $import,
                'courseOptions' => $courseOptions,
                'teacherOptions' => $teacherOptions,
            ]);
            
        }
        

        

        abort(404); 
            
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
  
    public function excelImport(Request $form)
    {
           
        $current_user=$this->currentUser();

        if(!$form->hasFile('schedules_file')){
           
            return   response()
                        ->json(['schedules_file' => ['無法取得上傳檔案'] 
                            ]  ,  422);      
        }

       
        $course_id=(int)$form['course_id'];
        $file=Input::file('schedules_file'); 

        $course=Course::findOrFail($course_id);

       
        $err_msg=$this->schedules->importSchedules($file,$course_id,$current_user);
        

        if($err_msg) {
             return response()->json(['error' => $err_msg,'code' => 422 ], 422);
         
        }

        return response()->json(['success' => true]);

       
    }
   
}
