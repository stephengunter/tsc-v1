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
        $model=$this->getNotices()->paginate(1);

        return response() ->json(['model' => $model  ]); 
       
    }

    
    public function latest()
    {
        $notices=$this->getNotices()->take(8)->get();

        return response() ->json(['notices' => $notices  ]);
       
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

    private function getNotices()
    {
        $notices=$this->notices->activeNotices()
                                ->where('public',true)
                                ->orderBy('date','desc');
        
        return $notices; 
    }
    
    
    
    
    
    
    
}
