<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

use App\Volunteer;

use App\Repositories\Volunteers;

use Illuminate\Support\Facades\Input;


class VolunteersImportController extends BaseController
 {
    protected $key='volunteers';

    public function __construct(Volunteers $volunteers)                               
    {
        
        $this->volunteers=$volunteers;
         
	}
	public function index()
    {
        
        if(request()->ajax()) abort(404);
       
        $menus=$this->menus($this->key);            
        return view('volunteers.import')->with([ 'menus' => $menus ]);        
       
    
    }

    public function store(Request $form)
    {
        
        $current_user=$this->currentUser();
        $file_name='volunteers_file';
        if(!$form->hasFile($file_name)){
            return   response()
                        ->json([$file_name => ['無法取得上傳檔案'] 
                            ]  ,  422);      
        }

        
        $file=Input::file($file_name); 

       
        $err_msg=$this->volunteers->importVolunteers($file,$current_user);
        

        if($err_msg) {
             return response()->json(['error' => $err_msg,'code' => 422 ], 422);
         
        }

        return response()->json(['success' => true]);

       
    }

    

	
}
