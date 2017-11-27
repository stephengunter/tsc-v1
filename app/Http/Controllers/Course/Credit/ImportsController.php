<?php

namespace App\Http\Controllers\Course\Credit;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

use App\CreditCourse;
use App\Term;
use App\Center;

use App\Services\Course\CreditCourseService;

use Illuminate\Support\Facades\Input;


class ImportsController extends BaseController
 {
    protected $key='credit_courses';

    public function __construct(CreditCourseService $creditCourseService)                               
    {
        
        $this->creditCourseService=$creditCourseService;
    }
    
	public function index()
    {
       
        $menus=$this->menus($this->key);  
        $typeOptions=$this->creditCourseService->typeOptions();
       
        return view('credit-courses.import')->with([ 'menus' => $menus,
                                                     'typeOptions' => $typeOptions
                                                   ]); 
                    
    
    }

    public function store(Request $form)
    {
        
        $current_user=$this->currentUser();
        
        if(!$current_user->isDev()){
            if(!$this->defaultCenter()) return  $this->unauthorized(); 
        }
        

        if(!$form->hasFile('credit_courses_file')){
            return   response()
                        ->json(['credit_courses_file' => ['無法取得上傳檔案'] 
                            ]  ,  422);      
        }

       
        $isUpdate=(int)$form['update'];
        $type=(int)$form['type'];
        $file=Input::file('courses_file'); 

        $err_msg='';
        if($isUpdate) $err_msg=$this->creditCourseService->imporCreditCourseInfoes($type,$file,$current_user);
        else   $err_msg=$this->creditCourseService->importCreditCourses($type,$file,$current_user);
        

        if($err_msg)   return response()->json(['error' => $err_msg,'code' => 422 ], 422);

        return response()->json(['success' => true]);

       
    }

   

	
}
