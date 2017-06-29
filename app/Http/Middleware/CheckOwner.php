<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use App\Exceptions\EmailUnconfirmed;

class CheckOwner
{
    protected $user;
   

    public function getOwner() {
        return   $this->user;
    }
    public function currentUser() {
        return   $this->user;
    }
    public function adminId() {
        return   $this->user->id;
    }
    public function canAdminCenter($center_id) {
        return $this->user->admin->centers->pluck('id')->contains($center_id);
    }
    public function centersCanAdmin()
    {
         return $this->user->admin->validCenters();
    }

    public function handle($request, Closure $next)
    {
        $user=request()->user();
        if(!$user){
           throw new AuthenticationException();
        }  
        if(!$user->isOwner()){
           throw new AuthenticationException();
        }
        if(!$user->email_confirmed){
            $email=$user->email;
            auth()->logout();
            throw new EmailUnconfirmed($email);
        }

        $user->admin->centers;
        $this->user=$user;
       
      
        return $next($request);
    }
}
