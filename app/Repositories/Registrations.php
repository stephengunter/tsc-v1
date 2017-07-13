<?php

namespace App\Repositories;

use App\User;
use App\Profile;
use App\Mail\EmailConfirmation;
use DB;

class Registrations 
{
    public function register($userValues)
    {
        $user= DB::transaction(function() 
        use($userValues){
              $user=User::create($userValues);
              $profile=new Profile();
              $profile->fullname=$userValues['name'];
              $user->profile()->save($profile);

              return $user;
              
        });
       

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