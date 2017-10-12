<?php

namespace App\Http\Controllers\Api\Teachers;

use App\Http\Controllers\Api\BaseController as BaseApiController;
use Auth;

class BaseController extends BaseApiController
{
    
    public function __construct()                     
    {
        $this->middleware(function ($request, $next) {
            
            $user = Auth::user();
            if(!$user){
                throw new \Illuminate\Auth\AuthenticationException();
            }  
            if(!$user->isTeacher()){
                 throw new \Illuminate\Auth\AuthenticationException();
            }
            if(!$user->email_confirmed){
                $email=$user->email;
                Auth::logout();
                throw new \App\Exceptions\EmailUnconfirmed($email);
            }
            return $next($request);
        });
       
    }
    
    
    protected function menus($current)
    {
        $key='teachers';
        return array(                 
            [
               'id'=> 1,
               'key' =>$key, 
               'text' => '教師資訊',
               'to' => '/teacher',
               'active' => $current =='/teacher',
            ],
            [
               'id'=> 2,
               'key' =>$key,
               'text' => '課程管理',
               'to' => '/teacher/courses',
               'active' => $current =='/teacher/courses',
           ],
        ); 
    }

    
}
