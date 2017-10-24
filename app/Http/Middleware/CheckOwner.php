<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use App\Exceptions\EmailUnconfirmed;

class CheckOwner
{
    public static function canLogin($user)
    {
        if(!$user) return false;

        if($user->isDev()) return true;
        if($user->isOwner()) return true;

        return false;
    }
    

    public function handle($request, Closure $next)
    {
        $user=request()->user();

        $can_login= static::canLogin($user);
        if($can_login){
            $this->user=$user;
           
            return $next($request);
        }else{

            return static::exceptions($user);

        }
        
        
        
    }
    public static function exceptions($user=null)
    {
        $email='';
        if($user){
            if(!$user->email_confirmed) $email=$user->email;
            auth()->logout();
        } 

        if($email)  throw new EmailUnconfirmed($email);

        throw new AuthenticationException();
    }

    
}
