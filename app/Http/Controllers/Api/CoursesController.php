<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Course;
use App\Center;

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
        $categoryId=(int)$request->category;       
        $centerId=(int)$request->center;

        $courses=[];

        $center=null;
        if($centerId)  $center=Center::find($centerId);   
        
        
        $area_id=0;
        if($center)$area_id=$center->area_id;            
        else $area_id=$this->centers->getAllAreas()[0]['value'];
            
        $oversea=false;
        $centers=$this->centers->index($oversea , $area_id)
                                ->get();
        
        if($center) $centerId= $center->id;
        else $centerId= $centers[0]->id;

        $categories= $this->categories->getFrontEndCategories($centerId)
                                      ->get();

                                      
        
        if(!$categoryId && count($categories)){
           $categoryId=$categories[0]->id;
            
        }  


        $courses= $this->getCourses($categoryId,$centerId);

        return response() ->json([ 
            'courses' => $courses , 
            'categories' => $categories,   
            'centers' => $centers
        ]); 




        // if($centerId && $categoryId){
        //     $courses= $this->getCourses($categoryId,$centerId);
        //     return response() ->json(['courses' => $courses  ]); 
        // }else{
            
        //     $area_id=$this->centers->getAllAreas()[0]['value'];
        //     $centers=$this->centers->index($oversea , $area_id)
        //                             ->get();
            
    
        //     $centerId=$centers[0]->id;
    
            
        //     $categories= $this->categories->getFrontEndCategories($centerId)
        //                                   ->get();
           
        //     $categoryId=$categories[0]->id;

        //     $courses= $this->getCourses($categoryId,$centerId);
        // }  

        
        

        
        
        // return response() ->json([ 
        //                             'courses' => $courses , 
        //                             'categories' => $categories,   
        //                             'centers' => $centers
        //                         ]); 
        
       
    }

    
    public function latest()
    {
        $courses=[];
        
        $category=$this->categories->findByCode('latest'); 
        if(!$category){
            return response() ->json(['courses' => $courses  ]); 
        }
        
        $center=0;
        return $this->getCourses($category->id,$center);

        return response() ->json(['courses' => $courses  ]); 
       
    }
    
    public function show($id)
    {
        $course = Course::with('center','term','teachers','classTimes','schedules')->findOrFail($id);
        
        $center=$course->center;
        $center->contactInfo=$center->contactInfo();
        $center->contactInfo->addressA=$center->contactInfo->addressA();

        $course->privateCategories=$course->privateCategories();

        // $defaultPhoto=false;
        // $course->photo= $course->photo($defaultPhoto);
        // $course->canSignup=true; //$course->canSignup();

        $course->populateViewData();
        
        // $course->classTimes= $course->classTimes->sortBy('weekday_id')
        //                                         ->sortBy('on')->values()->all();
        
        // foreach ($course->classTimes as $classTime) {
        //     $classTime->weekday;
        // }
        // foreach ($course->teachers as $teacher) {
        //       $teacher->name=$teacher->getName();
        //       $teacher->photo=$teacher->getPhoto();
        // }
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

    private function getCourses(int $categoryId,int $centerId)
    {
       
        $courses=$this->courses->getActiveCourses($categoryId,$centerId)->get();
        
        $canEditNumber=false;
        $photo=true;
        foreach ($courses as $course) {
            $course->populateViewData($canEditNumber,$photo);
            
        }

        return $courses;
        
    }
    
    
    
    
    
    
    
}
