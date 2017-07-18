<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Course;

use App\Repositories\Courses;
use App\Repositories\Teachers;
use App\Repositories\Categories;
use App\Repositories\Terms;
use App\Repositories\Centers;
use App\Repositories\Weekdays;

use App\Http\Requests\Course\CourseRequest;

use App\Support\Helper;

use App\Events\CourseUpdated;
use Carbon\Carbon;




class CoursesController extends BaseController
{
  
    public function __construct(Courses $courses, Categories $categories, Teachers $teachers,
                                 Terms $terms , Centers $centers, Weekdays $weekdays)
                               
    {
       
        $this->courses=$courses;
        $this->categories=$categories;
        $this->teachers=$teachers;
        $this->terms=$terms;
        $this->centers=$centers;
        $this->weekdays=$weekdays;

	}
    public function indexOptions()
    {
        $termOptions=$this->terms->options();
        $allCategories=$this->categories->getAll()->get();       
        $categoryOptions=$this->categories->optionsConverting($allCategories);
        $centerOptions=$this->centers->options();

        $weekdayOptions=$this->weekdays->options();

        return response()
            ->json([
                'termOptions' => $termOptions,
                'categoryOptions' => $categoryOptions,
                'centerOptions' => $centerOptions,
                'weekdayOptions' => $weekdayOptions

            ]);

    }
    public function index()
    {
        $request = request();
        $category=$request->category;       
        $center=$request->center;

        return $this->getCourses($category,$center);
       
    }

    
    public function latest()
    {
        $category=$this->categories->findByName('最新課程');          
        $center=0;
        return $this->getCourses($category->id,$center);
       
    }
    
    public function show($id)
    {
        $course = Course::with('center','term','teachers','classTimes','schedules')->findOrFail($id);
        
        $center=$course->center;
        $center->contactInfo=$center->contactInfo();
        $center->contactInfo->addressA=$center->contactInfo->addressA();

        $course->privateCategories=$course->privateCategories();
        $course->photo= $course->photo();
        $course->canSignup= $course->canSignup();
        
        $course->classTimes= $course->classTimes->sortBy('weekday_id')
                                                ->sortBy('on')->values()->all();
        
        foreach ($course->classTimes as $classTime) {
            $classTime->weekday;
        }
        foreach ($course->teachers as $teacher) {
              $teacher->name=$teacher->getName();
              $teacher->photo=$teacher->getPhoto();
        }
        return response()->json(['course' => $course]);
    }

    
    
    public function options()
    {
        $options=[];
        $request = request();
        $teacher_id=(int)$request->teacher; 
        if($teacher_id){
            $courseList=$this->courses->getByTeacher($teacher_id)->get();
            $options=$this->courses->optionsConverting($courseList);
            return response()  ->json(['options' => $options ]);   
        }
       
        
              
        $request = request();
        $term_id=(int)$request->term; 
        $center_id=(int)$request->center;

        $courseList=$this->courses->getAll()->where('term_id',$term_id)
                                     ->where('center_id',$center_id)
                                     ->get();
        $options=$this->courses->optionsConverting($courseList);    
        return response()->json(['options' => $options ]);     
    }

    
    public function search()
    {
        $courseList=[];
        $request=request();
        $keyword=$request->name;  
        
        $courseList=$this->courses->searchByName($keyword);
        return response()
                ->json([
                    'courseList' => $courseList->get()
                ]);  
        
    }

    private function getCourses($category,$center)
    {
        $categoryId=(int)$category;       
        $centerId=(int)$center;

        $courses=$this->courses->activeCourses();
        if($centerId){
            $courses=$courses->where('center_id',$centerId);       
        }
        $courses= $courses->whereHas('categories', function($query) use ($categoryId) {
                                            $query->where('id', $categoryId );
                                     });
        $courses= $courses->get();
        if(count($courses)){
            foreach ($courses as $course) {
                $course->photo= $course->photo();
                if(!$centerId){
                    $course->center;
                }
                
                foreach ($course->classTimes as $classTime) {
                    $classTime->weekday;
                }
            }           
        }
        
        
        return response() ->json(['courses' => $courses  ]); 
    }
    
    
    
    
    
    
    
}
