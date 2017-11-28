<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

use App\Course;
use App\Term;
use App\Center;

use App\Services\Course\CourseService;

use Illuminate\Support\Facades\Input;


class CoursesImportController extends BaseController
 {
    protected $key='courses';

    public function __construct(CourseService $courseService)                               
    {
        
        $this->courseService=$courseService;
    }
    
	public function index()
    {

        $menus=$this->menus($this->key); 
        return view('courses.import')->with([ 'menus' => $menus  ]); 
    }

    public function store(Request $form)
    {
        
        $current_user=$this->currentUser();
        
        if(!$current_user->isDev()){
            if(!$this->defaultCenter()) return  $this->unauthorized(); 
        }
        

        if(!$form->hasFile('courses_file')){
            return   response()
                        ->json(['courses_file' => ['無法取得上傳檔案'] 
                            ]  ,  422);      
        }

       
        $isUpdate=(int)$form['update'];
        $file=Input::file('courses_file'); 

        $err_msg='';
        if($isUpdate) $err_msg=$this->courseService->importCourseInfoes($file,$current_user);
        else   $err_msg=$this->courseService->importCourses($file,$current_user);
        

        if($err_msg)   return response()->json(['error' => $err_msg,'code' => 422 ], 422);

        return response()->json(['success' => true]);

       
    }

    // public function copy(Request $request)
    // {
    //     $current_user=$this->currentUser();
    //     $updated_by=$current_user->id;
    //     $values=$request->get('copy');
        
    //     $term_id=$values['term'];
    //     $center_id=$values['center'];
    //     $course_ids=$values['course_ids'];

    //     $selected_courses=Course::whereIn('id',$course_ids)->get();

    //     foreach ($selected_courses as $old_course) {

    //         $new_course=$this->courses->copyCourse($old_course,$term_id, $center_id,$updated_by); 

    //         if($old_course->groupAndParent()){

    //             $this->copySubCourses($old_course , $new_course,$updated_by);
           
    //         }
                                            
    //     }
        
    //     return response()->json(['saved' => true ]); 

       
    // }

    // private function copySubCourses($old_course , $new_course,$updated_by)
    // {
    //     $subCourses = $this->courses->subCourses($old_course->id)->get();
        
    //     foreach ($subCourses as $sub_course){
    //         $parent=$new_course->id;
    //         $this->courses->copyCourse($sub_course,$new_course->term_id, $new_course->center_id,$updated_by,$parent); 
    //     }
    // }

	
}
