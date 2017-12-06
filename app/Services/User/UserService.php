<?php
namespace App\Services\User;

use App\User;
use App\Profile;

use App\Repositories\Users;
use App\Repositories\Titles;
use App\Repositories\Registrations;


use App\Events\UserRegistered;

use App\Exceptions\RequestError;

use DB;
use Exception;

class UserService
{
    public function __construct(Users $users, Titles $titles,Registrations $registrations)                               
    {
          $this->users=$users;
          $this->titles=$titles;
         
          $this->registrations=$registrations;
          
    }
    public function getAll()
    {
        return $this->users->getAll();
    }
    public function delete($id,$current_user)
    {
        return $this->users->delete($id,$current_user);
        
    }
    public function findUsers($email, $phone)
    {
        return $this->users->findUsers($email, $phone);
    }
    public function canStore(User $user, Profile $profile)
    {
        if(!$user->email && !$user->phone){
            throw new RequestError('user.email','電話與email至少要填一個');
        }
        
    }

    public function store(User $user, Profile $profile)
    {
        $this->canStore($user,  $profile);

        $user= DB::transaction(function() use($user,$profile) {
            $user->save();
            
            $user->profile()->save($profile);

            return $user;
            
        });

        event(new UserRegistered($user));

        return $user;
    }

    public function update($userValues,$profileValues,User $user)
    {
        return $this->users->updateUserAndProfile($userValues,$profileValues, $user);
    }

   

   
}