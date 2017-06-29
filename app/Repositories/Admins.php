<?php

namespace App\Repositories;

use App\User;
use App\Admin;
use App\Role;

use App\Http\Requests\AdminRequest;
use App\Support\Support;

class Admins 
{
    public function adminRoleNames()
    {
        return Role::adminRoleNames();
       
    }

   

    public function initialize($user_id)
    {
        return [
             'user_id' => $user_id, 
             'role' => Role::adminRoleName()
			
        ];
    }
    public function getAll()
    {
          return Admin::where('removed',false);      
    }
    public function findOrFail($id)
    {
        $admin = Admin::findOrFail($id);
        return $admin;
       
    }

   
    public function getByCenter($center_id)
    {
        $admins=$this->getAll()->whereHas('centers', function($q) use ($center_id)
        {
            $q->where('id',$center_id );
        });
       
       return $admins;
   }
   public function store($user ,$values, $centerIds)
   {
        $admin=$user->admin;
        if(!$admin){
            $admin=new Admin($values);  
            $user->admin()->save($admin);
        }else{
            $user->admin->update($values);
        }

        $this->syncCenters($user->id,$centerIds);
        return $user;
    }

    public function update($id, $values)
    {
         $admin=$this->findOrFail($id);
        
         $admin->update($values);

         return $admin;
    }

    public function syncCenters($user_id,$centerIds)
    {
        $admin=$this->findOrFail($user_id);
        $admin->syncCenters($centerIds);
    }
    public function addToRole($id)
    {
         $admin=$this->findOrFail($id);
         $admin->addToRole();
    }
    public function removeRole($id)
    {
         $admin=$this->findOrFail($id);
         $admin->removeRole();
    }

    public function options($center)
    {
        $adminList=$this->getByCenter($center)->get();
        return $this->optionsConverting($adminList);
       
    }
    public function optionsConverting($adminList)
    {
        $options=[];
        foreach($adminList as $user)
        {
            $item=[ 'text' => $user->profile->fullname , 
                     'value' => $user->id , 
                 ];
            array_push($options,  $item);
        }

        return $options;
    }

    public function attachCenter($admin_id,$center_id)
    {
        $admin=$this->findOrFail($admin_id); 
        $admin->attachCenter($center_id);
    }
    public function detachCenter($admin_id,$center_id)
    {
        $admin=$this->findOrFail($admin_id); 
        $admin->detachCenter($center_id);

    }

  
   public function delete($id,$updated_by)
   {
         $admin=$this->findOrFail($id); 
          $values=[
            'removed' => 1,
            'updated_by' => $updated_by
         ];
        
         $admin->update($values);

         
   }
   
    
}