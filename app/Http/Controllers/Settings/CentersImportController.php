<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

use App\Repositories\Centers;

use App\Center;
use Illuminate\Support\Facades\Input;
use App\Support\Helper;

use DB;

class CentersImportController extends BaseController
{
    protected $key='main_settings';
    public function __construct(Centers $centers)
    {

        $this->centers=$centers;
       
       
	}

    

    public function index()
    {
        
        if(request()->ajax()) abort(404);
       
        $menus=$this->menus($this->key);            
        return view('centers.import')->with([ 'menus' => $menus ]);        
       
    
    }

    public function store(Request $form)
    { 
        
        $current_user=$this->currentUser();       
        
        if(!Center::canImport($current_user)){
            return  $this->unauthorized();  
        }

        if(!$form->hasFile('centers_file')){
            return   response()
                        ->json(['centers_file' => ['無法取得上傳檔案'] 
                            ]  ,  422);      
        }
       
        $file=Input::file('centers_file'); 
        

        $err_msg=$this->centers->importCenters($file,$current_user);
        if($err_msg) {
            return response()->json(['error' => $err_msg,'code' => 422 ], 422);
        
        }

        return response()->json(['success' => true]);

       
    }

    
}
