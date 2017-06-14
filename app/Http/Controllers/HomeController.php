<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Hash;
use Auth;
use App\User;

class HomeController extends BaseController
{
    
    public function __construct()
    {
          $exceptAdmin=[];
          $allowVisitors=[];
          $this->setMiddleware( $exceptAdmin, $allowVisitors);
        
    }

   
    public function index()
    {
        if(!request()->ajax()){
            return view('app');
         }  

        $keys=['signups','refunds','courses','users','teachers','discounts','settings'];
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
