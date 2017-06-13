<?php

namespace App\Repositories;

use App\User;
use App\Role;
use App\Center;
use App\Volunteer;

use App\Http\Requests\VolunteerRequest;

class Volunteers 
{
    public function getRoleName()
    {
        return Role::volunteerRoleName();
    }
    public function initialize($user_id)
    {
        return [
             'user_id' => $user_id, 
             'join_date' => ''
			
        ];
    }
    public function getAll()
    {
          return Volunteer::where('removed',false);      
    }
    public function findOrFail($id)
    {
        $volunteer = Volunteer::findOrFail($id);
        return $volunteer;
       
    }
   
    public function getByCenter($center_id)
    {
        $volunteers=$this->getAll()->whereHas('centers', function($q) use ($center_id)
        {
            $q->where('id',$center_id );
        });
       
       return $volunteers;
   }
   public function store($user ,$values,$centerIds)
   {
        $volunteer=$user->volunteer;
        if(!$volunteer){
            $volunteer=new Volunteer($values);  
            $user->volunteer()->save($volunteer);
        }else{
            $user->volunteer->update($values);
        }

        $this->syncCenters($user->id,$centerIds);
        return $user;
    }

    public function update($id, $values)
    {
         $volunteer=$this->findOrFail($id);
        
         $volunteer->update($values);

         return $volunteer;
    }

    public function syncCenters($user_id,$centerIds)
    {
        $volunteer=$this->findOrFail($user_id);
        $volunteer->syncCenters($centerIds);
    }
    public function options($center)
    {
        $volunteerList=$this->getByCenter($center)->get();
        return $this->optionsConverting($volunteerList);
       
    }
    public function optionsConverting($volunteerList)
    {
        $volunteerOptions=[];
        foreach($volunteerList as $user)
        {
            $item=[ 'text' => $user->profile->fullname , 
                     'value' => $user->id , 
                 ];
            array_push($volunteerOptions,  $item);
        }

        return $volunteerOptions;
    }

    public function attachCenter($volunteer_id,$center_id)
    {
            $volunteer=$this->findOrFail($volunteer_id); 
            $volunteer->attachCenter($center_id);

    }
    public function detachCenter($volunteer_id,$center_id)
    {
           $volunteer=$this->findOrFail($volunteer_id); 
           $volunteer->detachCenter($center_id);

    }

   public function delete($id,$admin_id)
   {
         $volunteer=$this->findOrFail($id); 
        
         $values=[
           
            'removed' => 1,
            'updated_by' => $admin_id
         ];
        
         $volunteer->update($values);
         
   }
  


}