<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

use App\Repositories\Centers;
use App\Repositories\Users;
use App\Http\Requests\Settings\CenterRequest;

use App\Center;

use App\Http\Middleware\CheckAdmin;
use App\Support\Helper;

use DB;

class CentersImportController extends BaseController
{
    protected $key='main_settings';
    public function __construct(Centers $centers, Users $users)
    {

        $this->centers=$centers;
        $this->users=$users;
       
	}

    

    public function index()
    {
        dd('yyy');
        if(request()->ajax()) abort(404);
       
        $menus=$this->menus($this->key);            
        return view('centers.import')->with([ 'menus' => $menus ]);        
       
    
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
