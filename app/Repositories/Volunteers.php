<?php

namespace App\Repositories;

use App\User;
use App\Profile;
use App\Role;
use App\Center;
use App\Volunteer;

use Carbon\Carbon;

use App\Http\Requests\VolunteerRequest;

use App\Events\UserRegistered;
use Excel;
use DB;

use App\Support\Helper;

class Volunteers 
{
    public function __construct(Users $users, Centers $centers)                          
    {
        $this->users=$users;  
        $this->centers=$centers; 
        
    }

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

    public function storeVolunteer($userValues,$profileValues,$volunteerValues,$user_id,$volunteerId)
    {
        $user= DB::transaction(function() 
            use($userValues,$profileValues,$volunteerValues,$user_id,$volunteerId)
            {
                $user=null;
                if($user_id){
                    $user=User::findOrFail($user_id);
                    $user->update($userValues);
                    $user->profile->update($profileValues);
                }else{
                    $user=new User($userValues);
                    $user->password= config('app.default_password');
                    $user->save();
                    $profile=new Profile($profileValues);
                    $user->profile()->save($profile);
                }

                
                if($volunteerId){
                    $volunteer = Volunteer::findOrFail($volunteerId);
                    $volunteer->update($volunteerValues);
                }else{
                    $volunteer=$user->volunteer;
                    if(!$volunteer){
                        $volunteer=new Volunteer($volunteerValues);  
                        $user->volunteer()->save($volunteer);
                    }else{
                        $user->volunteer->update($volunteerValues);
                    }
                }

                $user->addRole(Volunteer::roleName());
            
                return $user;
        });
        

        $is_new_user=true;
        if($user_id)  $is_new_user=false;

        if($is_new_user){
            event(new UserRegistered($user));            
        }


        $volunteer= Volunteer::findOrFail($user->id);

        return $volunteer;
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
        $volunteerList=$this->getByCenter($center);
        if(!count($volunteerList)){
            return [];
        }
        
        return $this->optionsConverting($volunteerList->get());
       
    }
    public function optionsConverting($volunteerList)
    {
        $volunteerOptions=[];
        foreach($volunteerList as $volunteer)
        {
            $item=[ 'text' => $volunteer->getName() , 
                     'value' => $volunteer->user_id , 
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

   public function importVolunteers($file,$current_user)
   {
       $err_msg='';
       
       
       $excel=Excel::load($file, function($reader) {             
           $reader->limitColumns(16);
           $reader->limitRows(100);
       })->get();

       $volunteerList=$excel->toArray()[0];

       for($i = 1; $i < count($volunteerList); ++$i) {
           $row=$volunteerList[$i];

           $fullname=trim($row['fullname']);
           if(!$fullname){
              continue;
           }

           

           $exist_user=null;
           $sid=trim($row['id']);
           if($sid){
               $exist_user=$this->users->findBySID(strtoupper($sid));
              
           }

           $gender=(int)trim($row['gender']);
           if($gender) $gender=true;
           else $gender=false;

           $dob=trim($row['dob']);
           if($dob){
              
                $ROC=true;
                $dob=Helper::getDateFromString($dob, $ROC);
               
           }
          

           $phone=trim($row['phone']);
           $email=trim($row['email']);

           if(!$exist_user){
               $userList=$this->users->findUsers($email, $phone);
               if(count($userList)) $exist_user=$userList[0];
           }

           $title=trim($row['title']);

           $updated_by=$current_user->id;
           
           $userValues=[
               'name' => $fullname,
               'email' => $email,
               'phone' => $phone,
               'updated_by' => $updated_by,
               'removed' => false
           ];
           $profileValues=[
               'fullname' => $fullname,
               'SID' => $sid,
               'gender' => $gender,
               'dob' => $dob,               
               'updated_by' => $updated_by,
               'title' => $title
             
           ];
           
           $user_id=0;
           if($exist_user) $user_id=$exist_user->getUserId();

           $volunteer_id=0;
           if($exist_user){
               if($exist_user->volunteer) $volunteer_id=$exist_user->getUserId();
           }

           $volunteerValues=[];

           $volunteer=$this->storeVolunteer($userValues,$profileValues,$volunteerValues,
           $user_id,$volunteer_id);
           
           if(!$volunteer) continue;


           $zipcode=trim($row['zipcode']);
           $street=trim($row['street']);

           if($zipcode){
               $volunteer->user->updateAddress($zipcode, $street,$updated_by);
           }
       }

       return $err_msg;
   }
  


}