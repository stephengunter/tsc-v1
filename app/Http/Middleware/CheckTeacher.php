<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use App\Exceptions\EmailUnconfirmed;

class CheckTeacher
{
    protected $user;

   
    public function currentUser() {
        return   $this->user;
    }
    

    public function handle($request, Closure $next)
    {
        $user=request()->user();
        if(!$user){
           throw new AuthenticationException();
        }  
        if(!$user->isTeacher()){
            throw new AuthenticationException();
        }
        if(!$user->email_confirmed){
            $email=$user->email;
            auth()->logout();
            throw new EmailUnconfirmed($email);
        }

       
        $this->user=$user;
       
        return $next($request);
    }

    
}
