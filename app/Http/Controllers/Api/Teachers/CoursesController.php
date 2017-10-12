<?php

namespace App\Http\Controllers\Api\Teachers;

use App\Http\Controllers\Api\Teachers\BaseController;
use Illuminate\Http\Request;
use App\Course;
use App\Repositories\Courses;
use App\Repositories\Teachers;

use App\Http\Requests\Teacher\TeacherUserRequest; 

use App\Support\Helper;
use DB;


class CoursesController extends BaseController
{
    
    public function __construct(Courses $courses)                     
    {
        parent::__construct();
        $this->courses=$courses;

    }

    public function index()
    {
        $current ='/teacher/courses';
        $menus=$this->menus($current);
          
        $current_user=$this->currentUser();
        $courses=$this->courses->getByTeacher($current_user->id)
                        ->with(['center','classTimes','status'])
                        ->orderBy('begin_date','desc')->get();
        
        if(count($courses)){
            foreach ($courses as $course) {
                
                foreach ($course->classTimes as $classTime) {
                  $classTime->weekday;
                }
                
            }
        }
        
        return response()->json([
            'menus' => $menus,
            'courses' => $courses,
        ]);
    }

    public function show($id)
    {
        $course = Course::with('center','term','categories','teachers','classTimes','status')->findOrFail($id);
        foreach ($course->classTimes as $classTime) {
            $classTime->weekday;
        }
        $course->classTimes= $course->classTimes->sortBy('weekday_id')
                                                ->sortBy('on')->values()->all();
        
        
        foreach ($course->teachers as $teacher) {
                $teacher->name=$teacher->getName();
        }
        
        return response()->json(['course' => $course]);
    }
}
