<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use App\Exceptions\EmailUnconfirmed;

class CheckAdmin
{
    protected $user;

    public function getAdmin() {
        return   $this->user->admin;
    }
    public function currentUser() {
        return   $this->user;
    }
    public function getAdminId() {
        return   $this->user->id;
    }
    public function isOwner() {
        return   $this->user->isOwner();
    }
    public function canAdminCenter($center_id) {
        return $this->user->admin->centers->pluck('id')->contains($center_id);
    }

    public function handle($request, Closure $next)
    {
        $user=request()->user();
        if(!$user){
           throw new AuthenticationException();
        }  
        if(!$user->isAdmin()){
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
