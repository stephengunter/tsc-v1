<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

use App\Category;

use App\Repositories\Categories;
use App\Repositories\Courses;
use App\Repositories\Terms;
use App\Repositories\Centers;

use App\Support\Helper;
use App\Http\Middleware\CheckAdmin;

class CategoryCourseController extends BaseController
{
    public function __construct(Categories $categories,Courses $courses,
                           Centers $centers, Terms $terms) 
    {
		
		 $this->categories=$categories;
         $this->terms=$terms;
         $this->courses=$courses;
         $this->centers=$centers;
	}

    
    public function index()
    {
        $request = request();
        $category_id=(int)$request->category;       
        $center_id=(int)$request->center;

        $current_user=$this->currentUser();
        $canEdit=Category::canEdit($current_user);

        $category=$this->categories->findOrFail($category_id);
        $courseList=$category->activeCourses();

        $activeTerms=$this->terms->activeTerms()->get();
        $courseList=$courseList->whereIn('term_id' , $activeTerms->pluck('id'));
                               
        if($center_id){
            $courseList=$courseList->where('center_id',$center_id);
        }

        $courseList = $courseList->with(['center','categories','teachers'])->get();
       
        if(count($courseList)){
            foreach ($courseList as $course) {
                foreach ($course->teachers as $teacher) {
                  $teacher->name=$teacher->getName();
                }
            }
        }



        return response()
        ->json([
            'courseList' => $courseList,
            'terms' => $activeTerms,
            'canEdit' => $canEdit
        ]);
        
        
       
    }
    public function create()
    {
        $current_user=$this->currentUser();
        $canEdit=Category::canEdit($current_user);

        if(!$canEdit)  return  $this->unauthorized(); 

        $request = request();
        $category_id=(int)$request->category;
        $category=$this->categories->findOrFail($category_id);
        $except_ids=$category->courses->pluck('id'); 

        $courseList=$this->courses->activeCourses();
        $activeTerms=$this->terms->activeTerms()->get()->pluck('id');
        $courseList=$courseList->whereIn('term_id' , $activeTerms);
        $courseList=$courseList->whereNotIn('id' , $except_ids);

        $courseList = $courseList->with(['center','categories','teachers'])->get();
       
        if(count($courseList)){
            foreach ($courseList as $course) {
                $course->getParentCourse();
                foreach ($course->classTimes as $classTime) {
                  $classTime->weekday;
                }
                foreach ($course->teachers as $teacher) {
                  $teacher->name=$teacher->getName();
                }
            }
        }                       
        return response()->json(['courseList' => $courseList  ]);
    }
   

    public function store(Request $request)
    {
        $current_user=$this->currentUser();
        $canEdit=Category::canEdit($current_user);

        if(!$canEdit)  return  $this->unauthorized(); 

        $category= $this->categories->findOrFail($request['category']);
        $courseIds=$request['courses'];
        for($i = 0; $i < count($courseIds); ++$i) {
            $category->attachCourse($courseIds[$i]);
        }
        return response()->json([ 'saved' => true ]);   
        
    }

    public function destroy($id)
    {
        $current_user=$this->currentUser();
        $canEdit=Category::canEdit($current_user);

        $request = request();
        $course_id = $request->course;

        $category= $this->categories->findOrFail($id);
       
        $category->detachCourse($course_id);
        
        return response()->json(['saved' => true ]);            
                   
    }

    

    
   
}
