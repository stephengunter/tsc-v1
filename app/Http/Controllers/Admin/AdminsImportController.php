<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

use App\Admin;

use App\Repositories\Admins;

use App\Support\Helper;

use Illuminate\Support\Facades\Input;

class AdminsImportController extends BaseController
{
    protected $key='admins';
    public function __construct(Admins $admins)                                
    {   
        
		$this->admins=$admins;
       
        
	}
    

    public function index()
    {
        if(request()->ajax()) abort(404);
       
        $menus=$this->menus($this->key);            
        return view('admins.import')->with([ 'menus' => $menus ]);        
       
    
    }

    public function store(Request $form)
    {
        $current_user=$this->currentUser();
        $center_id=0;
        if(!$current_user->isDev()){
            $defaultCenter=$this->defaultCenter();
            if($defaultCenter) $center_id=$defaultCenter->id;
            else return  $this->unauthorized();  
        }


        
        if($current_user->admin){
             $center=$current_user->admin->defaultCenter();
             if($center){
                 $center_id=$center->id;
             }
        }

        if(!$form->hasFile('admins_file')){
            return   response()
                        ->json(['admins_file' => ['無法取得上傳檔案'] 
                            ]  ,  422);      
        }

        $current_user=$this->currentUser();

        $file=Input::file('admins_file');
      

        $this->admins->importAdmins($file,$current_user);

        return response()->json(['success' => true]);

       
    }
    

    



    
   
}
