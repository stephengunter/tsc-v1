<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

use App\Course;

use App\Repositories\Courses;

use Illuminate\Support\Facades\Input;


class CoursesImportController extends BaseController
 {
    protected $key='courses';

    public function __construct(Courses $courses)                               
    {
        
        $this->courses=$courses;
         
	}
	public function index()
    {
        $from=request()->get('from');
        if(!$from) $from='';
       
        $menus=$this->menus($this->key);            
        return view('courses.import')->with([ 'menus' => $menus , 'from' => $from ]);        
       
    
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

        $group=(int)$form['group'];
        $isUpdate=(int)$form['update'];
        $file=Input::file('courses_file'); 

        $err_msg='';
        if($group){
            $err_msg=$this->courses->importGroupCourses($file,$isUpdate,$current_user);
        }else{
            $err_msg=$this->courses->importCourses($file,$isUpdate,$current_user);
        }  
        

        if($err_msg) {
             return response()->json(['error' => $err_msg,'code' => 422 ], 422);
         
        }

        return response()->json(['success' => true]);

       
    }

    

    

	
}
