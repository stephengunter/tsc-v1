<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;

use App\Http\Middleware\CheckAdmin;
use Illuminate\Http\Request;
use Hash;
use Auth;
use App\User;

class HomeController extends BaseController
{
    
    public function __construct(CheckAdmin $checkAdmin)
    {
        $exceptAdmin=[];
        $allowVisitors=[];
        $this->setMiddleware( $exceptAdmin, $allowVisitors);
          
        $this->setCheckAdmin($checkAdmin);
    }

   
    public function index()
    {
        if(!request()->ajax()){
            return view('app');
        }

        $current_user=$this->currentUser(); 

        $keys=['signups','refunds','courses','users',
                'teachers','discounts','settings',
                'notices','reports'];
                
        if($current_user->isOwner()){
            array_push($keys, 'admins');
        }
        $systems=[];
        for( $i=0; $i < count($keys); $i++ ){
           $menus=array(
             $keys[$i] => $this->menus($keys[$i])
           );
           $systems = array_merge($systems,$menus);
           
        }
        return response()
            ->json([
                'model' => $systems,
            ]);
    }
}
