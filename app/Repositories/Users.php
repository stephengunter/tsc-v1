<?php

namespace App\Repositories;

use DB;
use App\User;
use App\Role;
use App\Profile;

use App\Support\Helper;

class Users 
{
    public function initialize($with_password=false)
    {
        $user= [
             'id'=>0,
             'email' => '', 
			 'name' => '',
			 'phone' => '',
		     'profile' =>  Profile::initialize()
        ];

        if($with_password){
           $user['password']= '000000';
        }

        return $user;
    }
    public function getAll()
    {
         return User::where('removed',false);
    }
    public function findOrFail($id)
    {
         return User::findOrFail($id);
    }
    public function findByEmail($email)
    {
       return User::where('email',$email)->first();
    }
    public function getByIds($ids)
    {
        return User::whereIn('id', $ids);
    }

    public function index()
    {
         return User::with('profile')->with('roles');
    }
   

    public function store($userValues, $profileValues)
    {
        
        
        $user=User::create($userValues);

        $profile=new Profile($profileValues);
        $user->profile()->save($profile);

        return $user;
    }

   
    public function updateUserAndProfile($userValues,$profileValues, $user)
    {
        $user->update($userValues);
        $user->profile->update($profileValues);
        

        return $user;
    }
    public function updateUser($values, $id)
    {
        $user = User::findOrFail($id);
        $user->update($values);

        return $user;
    }
    public function updateProfile($values, $id)
    {
        $user = User::findOrFail($id);
        $user->profile->update($values);

        return $user;
    }
    
   
    public function findBySID($sid,$withUser)
    {
        $profile =Profile::where('SID',  $sid);
        if($withUser) return $profile->with('user')->first();
       
        return $profile->first();      
    }
    private function getRole($name)
    {
        $role=Role::where('name',$name)->firstOrFail();
        return  $role;
    }
     
    public function getByRole($roleName , $withProfile )
    {
        $role=$this->getRole($roleName);

        if($withProfile)   return $role->users()->with('profile')->get();
        return $role->users;
       
    }
    public function queryByRole($roleName , $withProfile )
    {
        $role=$this->getRole($roleName);
        
        if($withProfile)   return $role->users()->with('profile','profile.title');
        return $role->users();
       
    }

    public function addToRole($id,$roleName)
    {
        $user = User::findOrFail($id);
        $role=$this->getRole($roleName);
       
        $user->attachRole($role); 

        $user->roles;    
        return $user;
    }
    public function removeFromRole($id,$roleName)
    {
        $user = User::findOrFail($id);
        $role=$this->getRole($roleName);
        $user->detachRole($role);     
       
         $user->roles;    
         return $user;
    }
   
    public function options($role , $fullname)
    {
        $options=[];
        $withProfile=false;
        if($fullname) $withProfile=true;
        
        $userList=$this->getByRole($role , $withProfile);

        $name=$user->name;
        if($fullname) $name= $user->profile->fullname;

       
        foreach($userList as $user)
        {
            $item=[ 'text' => $user->name , 
                     'value' => $user->id , 
                 ];
            array_push($options,  $item);
        }

          return $options;
       
    }
    public function optionsConverting($userList)
    {
        $options=[];
        foreach($userList as $user)
        {
            $item=[ 'text' =>   $user->profile->fullname,
                     'value' => $user->id , 
                 ];
            array_push($options,  $item);
        }

        return $options;
    }
  
    public function delete($id,$current_user)
    {
        $user = User::findOrFail($id);
        if(!$user->canDeleteBy($current_user)){
             return false;
        }
       
        $user->removed=true;
        $user->active=false;
        $user->updated_by=$current_user->id;
          
        $user->save();

        return true;
        
    }

    
   
   
    
}