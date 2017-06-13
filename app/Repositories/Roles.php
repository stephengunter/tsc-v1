<?php

namespace App\Repositories;

use App\Role;

class Roles 
{
   
    public function getAll()
    {
         return Role::all();
    }
    public function getByName($name)
    {
         return Role::where('name',$name)->first();
    }
    public function getAdminRoles()
    {
        $adminRoleNames=Role::adminRoleNames();
        return Role::whereIn('name',$adminRoleNames);

    }
    
    
    public static function teacherRole()
    {
        $role=Role::where('name','Teacher')->firstOrFail();
        return $role;       
    }
    public static function volunteerRole()
    {
        $role=Role::where('name','Volunteer')->firstOrFail();
        return $role;       
    }
    
    public static function adminRole()
    {
        $role=Role::where('name','Admin')->firstOrFail();
        return $role;       
    }
   
    public static function studentRole()
    {
        $role=Role::where('name','Student')->firstOrFail();
        return $role;       
    }
   
    public static function ownerRole()
    {
        $role=Role::where('name','Owner')->firstOrFail();
        return $role;       
    }


    public function optionsConverting($roleList)
    {
        $options=[];
        foreach($roleList as $role)
        {
            $item=[ 'text' => $role->display_name , 
                     'value' => $role->name , 
                 ];
            array_push($options,  $item);
        }

        return $options;
    }
   
   
    
}