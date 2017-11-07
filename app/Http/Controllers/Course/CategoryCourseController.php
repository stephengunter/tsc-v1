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

        $canEditNumber=false;
        $courseList = $courseList->with(['center','categories','teachers'])->get();
        foreach ($courseList as $course) {
            $course->populateViewData($canEditNumber);
            
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

        $params = request()->toArray();
        $except_category = null;
        if(array_key_exists('except', $params)){
            $except_category_id=$params['except'];
            $except_category=$this->categories->findOrFail($except_category_id);

        }

        if(!$except_category) abort(404);

        $except_course_ids=$except_category->courses->pluck('id'); 

        
        $with=['center','categories','teachers','classTimes'];
        $courseList=$this->courses->index($params,$with);

        $courseList=$courseList->where('reviewed',true)->where('active',true)
                               ->whereNotIn('id',$except_course_ids) 
                               ->filterPaginateOrder();

        $parentCourse=null;
        $canEditNumber=false;

        foreach ($courseList as $course) {
            $course->populateViewData($canEditNumber);
            
        }

        return response() ->json([
                                    'model' => $courseList, 
                                    'parentCourse' => $parentCourse,
                                    'canEditNumber' => $canEditNumber 
                                 ]);  
                    
       
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
