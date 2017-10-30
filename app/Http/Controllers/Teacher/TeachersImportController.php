<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

use App\Teacher;

use App\Repositories\Teachers;

use Illuminate\Support\Facades\Input;


class TeachersImportController extends BaseController
 {
    protected $key='teachers';

    public function __construct(Teachers $teachers)                               
    {
        
        $this->teachers=$teachers;
         
	}
	public function index()
    {
        
        if(request()->ajax()) abort(404);
       
        $menus=$this->menus($this->key);            
        return view('teachers.import')->with([ 'menus' => $menus ]);        
       
    
    }

    public function store(Request $form)
    {
        
        $current_user=$this->currentUser();
        
        if(!$current_user->isDev()){
            if(!$this->defaultCenter()) return  $this->unauthorized(); 
        }
        

        if(!$form->hasFile('teachers_file')){
            return   response()
                        ->json(['teachers_file' => ['無法取得上傳檔案'] 
                            ]  ,  422);      
        }

        $type=(int)$form['type'];
        $file=Input::file('teachers_file'); 

        $err_msg='';
        if($type){
            $err_msg=$this->teachers->importTeachers($file,$current_user);
           
        }else{
            $err_msg=$this->teachers->importTeacherGroups($file,$current_user);
        }  
        

        if($err_msg) {
             return response()->json(['error' => $err_msg,'code' => 422 ], 422);
         
        }

        return response()->json(['success' => true]);

       
    }

    

	
}
