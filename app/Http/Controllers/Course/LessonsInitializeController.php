<?php

namespace App\Http\Controllers\Course;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Lesson;
use App\Course;
use App\ClassTime;

use App\Repositories\Lessons;
use App\Repositories\Courses;
use App\Repositories\Classtimes;
use App\Repositories\Classrooms;
use App\Repositories\Teachers;
use App\Repositories\Volunteers;
use App\Repositories\LessonParticipants;


use App\Support\Helper;
use App\Http\Middleware\CheckAdmin;

class LessonsInitializeController extends BaseController
{
    protected $key='lessons';
    public function __construct(Lessons $lessons, Courses $courses,
                                Teachers $teachers, Volunteers $volunteers,
                                Classtimes $classtimes , Classrooms $classrooms,  
                                LessonParticipants $lessonParticipants,CheckAdmin $checkAdmin)
    {
         $exceptAdmin=[];
         $allowVisitors=[];
         $this->setMiddleware( $exceptAdmin, $allowVisitors);
        

		 $this->lessons=$lessons;
         $this->courses=$courses;
         $this->teachers=$teachers;
         $this->volunteers=$volunteers;
         $this->classtimes=$classtimes;
         $this->classrooms=$classrooms;
         $this->lessonParticipants=$lessonParticipants;

         $this->setCheckAdmin($checkAdmin);
	}

    public function  create()
    {        
        $request = request();
        $course=(int)$request->course;
        
        if(!$course) abort(404);
       
        $course=$this->courses->findOrFail($course);   
        $current_user=$this->currentUser();
        
        if(!$course->canEditBy($current_user)){
            return  $this->unauthorized();   
        }

        $centerId=$course->center_id;
        $classroomOptions=$this->classrooms->options($centerId);

        $course->center;
        $course->schedules;
        $classTimes =$this->classtimes->getByCourse($course->id)->get();
        
                    
            return response()
                    ->json([
                        'course' => $course ,
                        'classroomOptions' => $classroomOptions,
                        'classTimes' => $classTimes
                    ]);  
    }
    public function store(Request $request)
    {
        
        $classroom_id=$request['classroom_id'];
        $course_id=$request['course_id'];

        $course=$this->courses->findOrFail($course_id);
        $current_user=$this->currentUser();
        
        if(!$course->canEditBy($current_user)){
            return  $this->unauthorized(); 
        }
        $classTimes=$course->classTimes;
        if(!$classTimes->count()){
            return   response()->json([
                         'classTimes' => ['缺少上課時間'] 
                      ]  ,  422);   
        }
        $updated_by=$current_user->id;
        $error=$this->lessons->initialize($course , $classroom_id,$updated_by);  
        if($error){
            return   response()->json($error ,  422); 
        }

        if(!$course->teachers){
             return response()->json([ 'saved' => true ]);       
        }
      
        $teacherIds=array_pluck( $course->teachers->toArray(), 'user_id');
        $withClassroom=false;
        $classLessons=$this->lessons->getByCourse($course_id,$withClassroom)
                                        ->where('status','>', -1)->get();
        foreach($classLessons as $lesson)
        {
             $this->addTeachers($lesson->id,$teacherIds,$updated_by);    
        }


          return response()
            ->json([
                'saved' => true 
            ]); 
      
    }

    private function addTeachers($lesson,$teacherIds,$updated_by)
    {
       if(empty($teacherIds)) return false;
        for($i = 0; $i < count($teacherIds); $i++) {
            $this->lessonParticipants->addTeacher($lesson , $teacherIds[$i] ,$updated_by);
        }
    }
    private function addVolunteers($lesson,$volunteerIds,$updated_by)
    {
        if(empty($volunteerIds)) return false;
        for($i = 0; $i < count($volunteerIds); $i++) {
            $this->lessonParticipants->addVolunteer($lesson , $volunteerIds[$i] ,$updated_by);
        }
    }

   
   
}
