<?php

namespace App\Http\Controllers\Course;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Lesson;
use App\Course;
use App\ClassTime;

use App\Http\Requests\Course\LessonRequest;


use App\Repositories\Lessons;
use App\Repositories\Courses;
use App\Repositories\Classtimes;
use App\Repositories\Classrooms;
use App\Repositories\Teachers;
use App\Repositories\Volunteers;
use App\Repositories\LessonParticipants;


use App\Support\Helper;
use App\Http\Middleware\CheckAdmin;

use Carbon\Carbon;

class LessonsController extends BaseController
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

    public function index()
    {
        $request = request();
          
        if(!$request->ajax()){
            $menus=$this->menus($this->key);            
            return view('lessons.index')
                    ->with(['menus' => $menus]);
        } 
        $course_id=(int)$request->course;
        $course=$this->courses->findOrFail($course_id);
        
        $canInit=$course->canInitLessons();
       
        $lessonList=$this->lessons->getAll()   
                                  ->with('classroom')
                                  ->where('course_id', $course_id)
                                  ->filterPaginateOrder();

        if(count($lessonList)){
           
            foreach ($lessonList as $lesson) {

                $lesson->teachers=$lesson->teachers();
                foreach ($lesson->teachers as $teacher) {
                     $teacher->name=$teacher->getName();
                }

                $lesson->volunteers=$lesson->volunteers();
                foreach ($lesson->volunteers as $volunteer) {
                     $volunteer->name=$volunteer->getName();
                }
            }
        }
        
         return response() ->json([
                                    'model' => $lessonList,
                                    'canInit' => $canInit  
                                  ]);  
       
       
    }

    
   
    public function create()
    {
        $course=request()->get('course');
        if(!$course) abort(404);

        $course=$this->courses->findOrFail($course);  
        $current_user=$this->currentUser();
        
        if(!$course->canEditBy($current_user)){
             return  $this->unauthorized();   
        }

        $lesson= Lesson::initialize($course);
        
        $teachers=$this->teachers->optionsConverting($course->teachers);
        $volunteers=[];
       
        

        $centerId=$course->center_id;
        $classroomOptions=$this->classrooms->options($centerId);
        $lesson['classroom_id']=$classroomOptions[0]['value'];
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
        $current_user=$this->currentUser();
        $removed=false;
        $updated_by=$current_user->id;

        $values=$request->getValues($updated_by,$removed);
        $course_id=$values['course_id']; 
        $course=Course::findOrFail($course_id);
        if(!$course->canEditBy($current_user)){
            return  $this->unauthorized();
        }
        
        if($request->isDayOff()){
            $fields=['course_id','date','status','ps' ,'removed','updated_by'];
            $values= array_only($values, $fields);           
           
            $lesson=$this->lessons->store($values);
            return response()->json($lesson);
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

   

    // public function dayOff(Request $request)
    // {
       
    //     $current_user=$this->checkAdmin->getAdmin();
    //     $removed=false;
    //     $updated_by=$current_user->id;
       
    //     $lessonValues = $request['lesson'];
    //     if (array_key_exists('id', $lessonValues)){
    //         $id=$lessonValues['id'];
    //         $lesson=Lesson::findOrFail($id); 
    //         if(!$lesson->canEditBy($current_user)){
    //             return   response()->json(['msg' => '權限不足' ]  ,  401);      
    //         }

    //         $lesson=$this->lessons->dayOff($id, $updated_by);
    //         return response()->json($lesson);
    //     }else{
    //          $values= array_only($lessonValues, ['course_id','date', 'status']);
    //          $course_id=$values['course_id']; 
    //          $course=Course::findOrFail($course_id);
    //          if(!$course->canEditBy($current_user)){
    //             return   response()->json(['msg' => '權限不足' ]  ,  401);    
    //          }

    //          $values=Helper::setUpdatedBy($values,$updated_by);
    //          $values=Helper::setRemoved($values,$removed);

    //          $lesson=$this->lessons->store($values);

    //           return response()->json($lesson);
            
    //     }
        
       
      
    // }

    public function show($id)
    {
        if(!request()->ajax()){
            $menus=$this->menus($this->key);            
            return view('lessons.details')
                    ->with([ 'menus' => $menus,
                              'id' => $id     
                        ]);
        }  
        $current_user=$this->currentUser();

        $lesson=Lesson::with('course','classroom')->findOrFail($id);
        $lesson->teachers=$lesson->teachers();
        foreach ($lesson->teachers as $teacher) {
                $teacher->name=$teacher->getName();
        }
        $lesson->volunteers=$lesson->volunteers();
        
        foreach ($lesson->volunteers as $volunteer) {
                $volunteer->name=$volunteer->getName();
        }

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
        
        $current_user=$this->currentUser();
        if(!$lesson->canEditBy($current_user)){
            return  $this->unauthorized();   
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

        $current_user=$this->currentUser();
        if(!$lesson->canEditBy($current_user)){
            return  $this->unauthorized();         
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
        $current_user=$this->currentUser();
        if(!$lesson->canDeleteBy($current_user)){
            return  $this->unauthorized();  
        }

        $lesson->removed=1;
        $lesson->updated_by=$current_user->id;
        $lesson->save();

        return response()->json(['deleted' => true]);
            
    }


    

   
   
}
