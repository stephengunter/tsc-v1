<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Route;
use Auth;

class BaseController extends Controller
{
   
    public function __construct()
    {
		  
        
    }
    
    
    protected function unauthorized()
    {
        throw new \Illuminate\Auth\AuthenticationException();
        //return  response()->json(['msg' => '權限不足','code' => 401 ]  ,  401);  
                                   
    }
    protected function currentUser()
    {
        return Auth::user();
      
        
    }
    
   
}
