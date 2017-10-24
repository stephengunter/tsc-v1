<?php

namespace App\Repositories;

use App\User;
use App\Profile;
use App\Admin;
use App\Role;

use App\Repositories\Users;

use App\Http\Requests\AdminRequest;
use App\Support\Support;

use Excel;
use DB;

use App\Events\AdminCreated;
use App\Events\AdminDeleted;
use App\Events\UserRegistered;


class Admins 
{
    public function __construct(Users $users)                          
    {
        $this->users=$users;  
        
    }

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
    public function storeAdmin($userValues,$profileValues,$adminValues,$user_id,$adminId,$current_user,$center_id)
    {
        $user= DB::transaction(function() 
            use($userValues,$profileValues,$adminValues,$user_id,$adminId)
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

                
                if($adminId){
                    $admin = Admin::findOrFail($adminId);
                    $admin->update($adminValues);
                }else{
                    $admin=$user->admin;
                   
                    if(!$admin){
                       
                        $admin=new admin($adminValues);  
                        $user->admin()->save($admin);
                    }else{
                        $user->admin->update($adminValues);
                    }
                }
            
                return $user;
            });



        $admin= Admin::findOrFail($user->id);
        if($center_id){
            $admin->attachCenter($center_id);
        }
       
       
        event(new AdminCreated($admin, $current_user));

        $is_new_user=true;
        if($user_id)  $is_new_user=false;

        if($is_new_user){
            event(new UserRegistered($user));            
        }
        
        return $admin;
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
    public function importAdmins($file,$current_user)
    {
        

        Excel::load($file, function($reader) use ($current_user){
           $adminList=$reader->get()->toArray()[0];
           for($i = 1; $i < count($adminList); ++$i) {
               $row=$adminList[$i];
               
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
                   $pieces=explode('/', $dob);
                   $year = (int)$pieces[0] + 1911;
                   $dob= $year . '/'.$pieces[1]. '/'.$pieces[2];
                   
               }

               $phone=trim($row['phone']);
               $email=trim($row['email']);

               if(!$exist_user){
                   $userList=$this->users->findUsers($email, $phone);
                   if(count($userList)) $exist_user=$userList[0];
               }

               $role=trim($row['role']);
               if(strtolower($role) == strtolower(Role::ownerRoleName()) ){
                   $role=Role::ownerRoleName();
               }else{
                   $role=Role::adminRoleName();
               }

               $active=(int)trim($row['active']);
               if($active){
                   $active=1;
               }else{
                   $active=0;
               }

               $updated_by=$current_user->id;
       
               $adminValues=[
                   'role' => $role,
                   'active' => $active,                 
                   'updated_by' => $updated_by,
                   'removed' => false
               ];
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
                 
               ];
               
               $user_id=0;
               if($exist_user) $user_id=$exist_user->getUserId();

               $admin_id=0;
               if($exist_user){
                   if($exist_user->admin) $admin_id=$exist_user->getUserId();
               }

               $center_id=0;
               if($current_user->admin){
                    $center=$current_user->admin->defaultCenter();
                    if($center){
                        $center_id=$center->id;
                    }
               }
               

               $admin=$this->storeAdmin($userValues,$profileValues,$adminValues,$user_id,$admin_id,$current_user,$center_id);

               
               if(!$admin) continue;

               $zipcode=trim($row['zipcode']);
               $street=trim($row['street']);

               if($zipcode){
                   $admin->user->updateAddress($zipcode, $street,$updated_by);
               }
               
              
           }  //end for  

          
       });

       



   }
   
    
}