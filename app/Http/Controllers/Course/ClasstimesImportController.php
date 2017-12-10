<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

use App\Course;

use App\Repositories\Classtimes;
use App\Repositories\Centers;

use Illuminate\Support\Facades\Input;


class ClasstimesImportController extends BaseController
 {
    protected $key='courses';

    public function __construct(Centers $centers,Classtimes $classtimes)                               
    {
        $this->centers=$centers;
        $this->classtimes=$classtimes;
    }
    
	public function index()
    {
        
        $centerOptions=$this->centers->optionsConverting($this->canAdminCenters());
       
        $menus=$this->menus($this->key);            
        return view('classtimes.import')->with([ 'menus' => $menus ,
                                                 'centerOptions' => $centerOptions
                                                ]); 
    }

    public function store(Request $form)
    {
        
        $current_user=$this->currentUser();
       

        if(!$form->hasFile('classtimes_file')){
            return   response()
                        ->json(['classtimes_file' => ['無法取得上傳檔案'] 
                            ]  ,  422);      
        }

        $file=Input::file('classtimes_file');   

        $err_msg=$this->classtimes->importAll($file,$current_user);
        
         if($err_msg) {
              return response()->json(['error' => $err_msg,'code' => 422 ], 422);
          
         }

        return response()->json(['success' => true]);

       
    }

	
}
