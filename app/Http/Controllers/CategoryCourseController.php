<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Repositories\Categories;
use App\Repositories\Courses;
use App\Repositories\Terms;
use App\Repositories\Centers;

use App\Support\Helper;
use App\Http\Middleware\CheckAdmin;

class CategoryCourseController extends Controller
{
    public function __construct(Categories $categories,Courses $courses,
                           Centers $centers, Terms $terms,CheckAdmin $checkAdmin) 
    {
		 $this->middleware('admin');
         $this->checkAdmin=$checkAdmin;

		 $this->categories=$categories;
         $this->terms=$terms;
         $this->courses=$courses;
         $this->centers=$centers;
	}

    public function indexOptions()
    {
        $activeTerms=$this->terms->activeTerms()->get();
        $termOptions=$this->terms->optionsConverting($activeTerms);

        $centerOptions=$this->centers->options();

        return response()
            ->json([
                'termOptions' => $termOptions,
                'centerOptions' => $centerOptions
            ]);
    }
    public function index()
    {
         $request = request();
         $termId=(int)$request->term; 
         $categoryId=(int)$request->category;       
         $centerId=(int)$request->center;
         $weekdayId=0;
         
         $courseList=$this->courses->index($termId,$categoryId,$centerId,$weekdayId)
                                        ->where('active',true)->orderBy('open_date')->get();
        
         foreach ($courseList as $course) {
            
              foreach ($course->classTimes as $classTime) {
                $classTime->weekday;
             }
         }
           return response()
            ->json([
                'courseList' => $courseList
            ]);
       
    }
    public function courseNotInCategory()
    {
         $request = request();
         $termId=(int)$request->term; 
         $categoryId=(int)$request->category;       
         $centerId=(int)$request->center;
         $active=(int)$request->active;

         $courseList=$this->courses->courseNotInCategory($termId,$categoryId,$centerId,$active)->get();
                                        
        
         foreach ($courseList as $course) {
            
              foreach ($course->classTimes as $classTime) {
                $classTime->weekday;
             }
         }
           return response()
            ->json([
                'courseList' => $courseList
            ]);
    }

    public function import(Request $request)
    {
         $category= $this->categories->findOrFail($request['category']);
         $courseIds=$request['courseIds'];
         for($i = 0; $i < count($courseIds); ++$i) {
            $category->attachCourse($courseIds[$i]);
         }
          return response()
            ->json([
                'saved' => true
            ]);
        
    }

    public function remove(Request $request)
    {
         $category= $this->categories->findOrFail($request['category']);
       
         $category->detachCourse($request['course']);
        
            return response()
                    ->json([
                        'saved' => true
                    ]);
    }

    

    
   
}
