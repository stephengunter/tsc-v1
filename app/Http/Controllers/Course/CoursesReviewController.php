<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\BaseController;

use App\Http\Requests\Course\CourseRequest;
use Illuminate\Http\Request;

use App\Course;
use App\User;
use App\Profile;
use App\Address;

use App\Repositories\Courses;
use App\Repositories\Centers;

use App\Support\Helper;

use Illuminate\Support\Facades\Input;

class CoursesReviewController extends BaseController
 {
    protected $key='courses';

    public function __construct(Courses $courses ,Centers $centers)                                                               
    {
        $this->courses=$courses; 
        $this->centers=$centers; 
         
	}
	public function index()
    {
        
        if(!request()->ajax()) {
            $centerOptions=$this->centers->optionsConverting($this->canAdminCenters());
            $menus=$this->menus($this->key);            
            return view('courses.review')->with([ 
                                                    'menus' => $menus ,
                                                    'centerOptions' => $centerOptions
                                                ]); 

        }      

        
        $params = request()->toArray();

        $with=['center','categories','teachers','classTimes'];
        $courses=$this->courses->index($params,$with)
                                ->orderBy('begin_date')
                                ->get(); 

                                 

        $current_user=$this->currentUser();
        $courseList=[];
        foreach ($courses as $course) {
            if($course->canReviewBy($current_user)){
                $course->populateViewData();
                array_push($courseList, $course);
            }
           
        }
        
             //不分頁
        $model=$this->initPaginator($courseList);
     
       
        

        return response() ->json([
                                    'model' => $model, 
                                    'canEditNumber' => false 
                                 ]);  
    }
  
    public function store(Request $form)
    {
        $current_user=$this->currentUser();
        $course_ids=$form['course_ids'];
        $reviewed=true;

        $err_msg='';
        if(count($course_ids)){
            for($i = 0;  $i< count($course_ids);  ++$i) {
                $id=$course_ids[$i];
                $course=Course::findOrFail($id);
                $this->courses->updateReview($id,$reviewed,$current_user);
            }
        }
       
        return response()->json(['success' => true ]); 

        
    }

    
    public function update(Request $form,$id)
    {
        
        $current_user=$this->currentUser();
        $course = Course::findOrFail($id);
        if(!$course->canReviewBy($current_user)){
            return  $this->unauthorized();    
        }

        
        $reviewed=$form['reviewed'];

        $current_user=$this->currentUser();
       
        $course=$this->courses->updateReview($id,$reviewed,$current_user);    

        return response()->json($course); 
         
           
    }
    

	
}
