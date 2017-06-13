<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lesson;
use App\Course;
use App\ClassTime;

use App\Http\Requests\LessonRequest;


use App\Repositories\Lessons;
use App\Repositories\Courses;
use App\Repositories\Classtimes;
use App\Repositories\Classrooms;
use App\Repositories\Teachers;
use App\Repositories\Volunteers;
use App\Repositories\LessonParticipants;


use App\Support\Helper;
use App\Http\Middleware\CheckAdmin;

class LessonsController extends Controller
{
    public function __construct(Lessons $lessons, Courses $courses,
                                Teachers $teachers, Volunteers $volunteers,
                                Classtimes $classtimes , Classrooms $classrooms,  
                                LessonParticipants $lessonParticipants,CheckAdmin $checkAdmin)
    {
         $this->middleware('admin');

		 $this->lessons=$lessons;
         $this->courses=$courses;
         $this->teachers=$teachers;
         $this->volunteers=$volunteers;
         $this->classtimes=$classtimes;
         $this->classrooms=$classrooms;
         $this->lessonParticipants=$lessonParticipants;

         $this->checkAdmin=$checkAdmin;
	}

    public function index()
    {
        
       $course=request()->get('course');
       if(!$course) abort(404);
       
       $lessonList=$this->lessons->index($course);

        return response()
        ->json([
            'lessonList' => $lessonList
        ]);
       
    }

    public function  initializeForm($course)
    {
         $course=$this->courses->findOrFail($course);   
         $current_user=$this->checkAdmin->getAdmin();
        
         if(!$course->canEditBy($current_user)){
            return   response()->json(['msg' => '權限不足' ]  ,  401);    
         }

         $course->center;
         $course->schedules;
         $classTimes =$this->classtimes->getByCourse($course->id);
                    
           return response()
                    ->json([
                        'course' => $course ,
                        'classTimes' => $classTimes
                    ]);  
    }
    public function  initialize(Request $request)
    {
         $classroom_id=$request['classroom_id'];
         $course_id=$request['course_id'];

         $course=$this->courses->findOrFail($course_id);
         $current_user=$this->checkAdmin->getAdmin();
        
         if(!$course->canEditBy($current_user)){
            return   response()->json(['msg' => '權限不足' ]  ,  401);    
         }
         $updated_by=$current_user->id;
         $this->lessons->initialize($course , $classroom_id,$updated_by);  

         if(!$course->teachers){
             return response()
                    ->json([
                        'saved' => true 
                    ]); 
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
   
    public function create()
    {
       
        $course=request()->get('course');
        if(!$course) abort(404);

        $course=$this->courses->findOrFail($course);  
        $current_user=$this->checkAdmin->getAdmin();
        
         if(!$course->canEditBy($current_user)){
            return   response()->json(['msg' => '權限不足' ]  ,  401);    
         }

        $lesson= Lesson::initialize($course);
        
        
        $teachers=$this->teachers->optionsConverting($course->teachers);
        $volunteers=[];

        $centerId=$course->center_id;
        $classroomOptions=$this->classrooms->options($centerId);
        
       
        $lesson['classroom_id'] = $classroomOptions[0]['value'];

        $volunteerOptions=$this->volunteers->options($centerId);
        $teacherOptions=$this->teachers->optionsConverting($course->teachers);

        return response()
            ->json([
                'lesson' => $lesson ,
                'course' => $course,
                'teachers' => $teachers,
                'volunteers' => [],
                'classroomOptions' => $classroomOptions,
                'volunteerOptions' => $volunteerOptions,
                'teacherOptions' => $teacherOptions
            ]);
            
    }
    public function store(LessonRequest $request)
    {
        $current_user=$this->checkAdmin->getAdmin();
        $removed=false;
        $updated_by=$current_user->id;
        $values=$request->getValues($updated_by,$removed);

        $course_id=$values['course_id']; 
        $course=Course::findOrFail($course_id);
        if(!$course->canEditBy($current_user)){
            return   response()->json(['msg' => '權限不足' ]  ,  401);    
        }

        $lesson=$this->lessons->store($values);

        $teacherIds=$request->getTeacherIds();
        $this->addTeachers($lesson->id,$teacherIds,$updated_by);

        $volunteerIds=$request->getVolunteerIds();
        $this->addVolunteers($lesson->id,$volunteerIds,$updated_by);

         return response()->json($lesson);
      
    }
    private function addTeachers($lesson,$teacherIds,$updated_by)
    {
       if(empty($teacherIds)) return false;
        for($i = 0; $i < count($teacherIds); $i++) {
                $values=[
                    'lesson_id' => $lesson,
                    'user_id' => $teacherIds[$i],
                    'status' => 0,  
                    'updated_by' => $updated_by           
                ];

            $this->lessonParticipants->addTeacher($values);
        }
    }
    private function addVolunteers($lesson,$volunteerIds,$updated_by)
    {
        if(empty($volunteerIds)) return false;
        for($i = 0; $i < count($volunteerIds); $i++) {
                $values=[
                    'lesson_id' => $lesson,
                    'user_id' => $volunteerIds[$i],
                    'status' => 0,     
                    'updated_by' => $updated_by               
                ];

            $this->lessonParticipants->addVolunteer($values);
        }
    }

   

    public function dayOff(Request $request)
    {
       
        $current_user=$this->checkAdmin->getAdmin();
        $removed=false;
        $updated_by=$current_user->id;
       
        $lessonValues = $request['lesson'];
        if (array_key_exists('id', $lessonValues)){
            $id=$lessonValues['id'];
            $lesson=Lesson::findOrFail($id); 
            if(!$lesson->canEditBy($current_user)){
                return   response()->json(['msg' => '權限不足' ]  ,  401);      
            }

            $lesson=$this->lessons->dayOff($id, $updated_by);
            return response()->json($lesson);
        }else{
             $values= array_only($lessonValues, ['course_id','date', 'status']);
             $course_id=$values['course_id']; 
             $course=Course::findOrFail($course_id);
             if(!$course->canEditBy($current_user)){
                return   response()->json(['msg' => '權限不足' ]  ,  401);    
             }

             $values=Helper::setUpdatedBy($values,$updated_by);
             $values=Helper::setRemoved($values,$removed);

             $lesson=$this->lessons->store($values);

              return response()->json($lesson);
            
        }
        
       
      
    }

    public function show($id)
    {
        $current_user=$this->checkAdmin->getAdmin();

        $lesson=Lesson::with('course','classroom')->findOrFail($id);
        $lesson->teachers=$lesson->teachers();
        $lesson->volunteers=$lesson->volunteers();

         $lesson->canEdit=$lesson->canEditBy($current_user);
         $lesson->canDelete=$lesson->canDeleteBy($current_user);

         return response()
                ->json([
                    'lesson' => $lesson
                ]);
       
    }
    public function edit($id)
    {
        $lesson=Lesson::findOrFail($id); 
        
        $current_user=$this->checkAdmin->getAdmin();
        if(!$lesson->canEditBy($current_user)){
            return   response()->json(['msg' => '權限不足' ]  ,  401);      
        }

        $course=$this->courses->findOrFail($lesson->course_id);

        $teachers=$lesson->teachers();
        if($teachers){
            $teachers=$this->teachers->optionsConverting($teachers);
        }else{
            $teachers=[];
        }

        $volunteers=$lesson->volunteers();
        if($volunteers){
            $volunteers=$this->volunteers->optionsConverting($volunteers);
        }else{
            $volunteers=[];
        }

        $centerId=$course->center_id;

        $classroomOptions=$this->classrooms->options($centerId);

        $volunteerOptions=$this->volunteers->options($centerId);

        $teacherOptions=$this->teachers->optionsConverting($course->teachers);
        return response()
            ->json([
                'lesson' => $lesson ,
                'course' => $course,
                'teachers' => $teachers,
                'volunteers' => $volunteers,
                'classroomOptions' => $classroomOptions,
                'volunteerOptions' => $volunteerOptions,
                'teacherOptions' => $teacherOptions
            ]);
  
    }
    public function update(LessonRequest $request, $id)
    {
        $lesson=Lesson::findOrFail($id); 
        
        $current_user=$this->checkAdmin->getAdmin();
        if(!$lesson->canEditBy($current_user)){
            return   response()->json(['msg' => '權限不足' ]  ,  401);      
        }

        $removed=false;
        $updated_by=$current_user->id;

        $values=$request->getValues($updated_by,$removed);

        $lesson=$this->lessons->update($id , $values);

        $teacherIds=$request->getTeacherIds();
        $this->lessonParticipants->syncTeachers($lesson,$teacherIds);

        $volunteerIds=$request->getVolunteerIds();
        $this->lessonParticipants->syncVolunteers($lesson,$volunteerIds);

         return response()->json($lesson);

    }
    public function destroy($id)
    {
        $lesson=Lesson::findOrFail($id); 
        $current_user=$this->checkAdmin->getAdmin();
        if(!$lesson->canDeleteBy($current_user)){
            return   response()->json(['msg' => '權限不足' ]  ,  401);    
        }
        $this->lessons->delete($id,$current_user->id);

        return response()
            ->json([
                'deleted' => true
            ]);
    }


    

   
   
}
