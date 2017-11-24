<?php
namespace App\Services\User;

use App\User;
use App\Profile;
use App\Repositories\Users;
use Exception;

use App\Exceptions\RequestError;

use DB;

class UserService
{
   public function __construct(Users $users)
   {
       $this->users=$users;
       
   }

   public function store(User $user, Profile $profile)
   {
      $user= DB::transaction(function() use($user,$profile) {
         $user->save();
        
         $user->profile()->save($profile);

         return $user;
          
      });

      return $user;
   }

   

   
}