<?php

namespace App\Repositories;

use App\User;
use App\Profile;

use App\Mail\EmailConfirmation;

class Registrations 
{
    public function register($userValues)
    {
        $user=User::create($userValues);
       
        $profileValues=[
            'fullname'=>$user->name
        ];
        $profile=new Profile($profileValues);
        $user->profile()->save($profile);

        return $user;
           
    }

    public function confirmEmail($user_id, $token)
    {
        $user= User::findOrFail($user_id);
        if($user->email_confirmed)  return true;
        if(!$user->emailToken) return false;
        if(!$user->emailToken->isValid()) return false;

        if($token!=$user->emailToken->token) return false;

        $user->email_confirmed=true;
        $user->save();

        return true;

    }

    
   
    
}