<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;

use App\Notice;
use App\Course;
use App\Repositories\Notices;

use App\Support\Helper;
use Carbon\Carbon;

class NoticesController extends BaseController
{
  
    public function __construct(Notices $notices)                                          
    {
         $this->notices=$notices;

	}
    
    public function index()
    {
        $notices=$this->notices->getAll()->paginate(15);

        return response() ->json(['notices' => $notices  ]); 
       
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

    private function getNotices($category,$center)
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
