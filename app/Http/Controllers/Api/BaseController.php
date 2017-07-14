<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Route;

class BaseController extends Controller
{
   
    public function __construct()
    {
		  
          
	}
   
    
    protected function unauthorized()
    {
        return  response()->json(['msg' => '權限不足' ]  ,  401);  
    }
    protected function currentUser()
    {
        return request()->user();
        
    }
   
}
