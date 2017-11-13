<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\BaseController;

use App\Http\Middleware\CheckAdmin;

use Illuminate\Http\Request;

use App\User;
use App\Role;
use App\Teacher;
use App\Center;
use App\Volunteer;
use App\Admin;

use App\Repositories\Users;
use App\Repositories\Centers;

use App\Support\Helper;


class UserCenterController extends BaseController
 {
    
    
    public function __construct(Users $users,Centers $centers)
    {
        $this->users=$users;
        $this->centers=$centers;
	}

	public function index()
    {
       
        $current_user=$this->currentUser();
        $request = request();
        
        $role=$request->role; 
        $user_id=(int)$request->user;

        if(!$role || !$user_id) abort(404);
        $user =$this->users->findOrFail($user_id);

        $role=strtolower($role);
        $centers=[];
        if( $role == strtolower(Role::teacherRoleName()) ){
            
            $teacher=$user->teacher;
            $centers=$teacher->validCenters();   
            $canEdit=$teacher->canEditBy($current_user);   

            if($canEdit){
                $centersCanAdd=$teacher->centersCanAddByUser($current_user);
                if(Helper::isNullOrEmpty($centersCanAdd)) $canEdit=false;
            }

            return response()->json([
                'centers' => $centers ,
                'canEdit' => $canEdit
            ]);

        }else if( $role == strtolower(Role::volunteerRoleName()) ){

            $centers=$user->volunteer->validCenters();

        }else if(in_array($role, Role::adminRoleNames(true))){

            $centers=$user->admin->validCenters();
            $canEdit=$this->headCenterAdmin();
            return response()->json([
                                        'centers' => $centers ,
                                        'canEdit' => $canEdit
                                    ]);
        }
        

        return response()->json(['centers' => $centers ]);
       
    }
    public function create()
    {
        $current_user=$this->currentUser();
       
        $request = request();
        
        $role=$request->role; 
        $user_id=(int)$request->user;

        if(!$role || !$user_id) abort(404);
        $user =$this->users->findOrFail($user_id);

        $role=strtolower($role);
        $options=[];
        $centersCanAdd=[];
       
        if( $role == strtolower(Role::teacherRoleName()) ){
           $centersCanAdd=$user->teacher->centersCanAddByUser($current_user);

        }else if( $role == strtolower(Role::volunteerRoleName()) ){

            $centersCanAdd=$user->volunteer->centersCanAddByUser($current_user);

        }else if(in_array($role, Role::adminRoleNames(true))){
            
            $centersCanAdd=$user->admin->centersCanAddByUser($current_user);

        }else{
            abort(404);
        }
        
        if(count($centersCanAdd)){
            $options=$this->centers->optionsConverting($centersCanAdd);
        }

        return response()->json(['options' => $options ]);
            
    }
    
    public function store(Request $request)
    {
        $current_user=$this->currentUser();
        
        $user_id=$request['id'];
        $center_id=$request['center'];
        $role=$request['role'];

        $user =$this->users->findOrFail($user_id);

        $role=strtolower($role);

        if( $role == strtolower(Role::teacherRoleName()) ){
            $user->teacher->attachCenter($center_id);
        }else if( $role == strtolower(Role::volunteerRoleName()) ){
            $user->volunteer->attachCenter($center_id);
        }else if(in_array($role, Role::adminRoleNames(true))){
            if(!$current_user->admin->isHeadCenterBoss())   return  $this->unauthorized();  
            
            $user->admin->attachCenter($center_id);
        }else{
            abort(404);
        }
        
        return response()->json([ 'saved' => true ]);   
      
    }
   
    public function destroy($id)
    {
        $current_user=$this->currentUser();

        $request = request();
        $role=$request->role;
        $center_id=$request->center;

        $role=strtolower($role);
        if( $role == strtolower(Role::teacherRoleName()) ){
            $teacher=Teacher::findOrFail($id);
            $teacher->detachCenter($center_id);
        }else if( $role == strtolower(Role::volunteerRoleName()) ){
            $volunteer=Volunteer::findOrFail($id);
            $volunteer->detachCenter($center_id);
        }else if(in_array($role, Role::adminRoleNames(true))){
            if(!$current_user->admin->isHeadCenterBoss())   return  $this->unauthorized();  


            $admin=Admin::findOrFail($id);
            $admin->detachCenter($center_id);
        }else{
            abort(404);
        }
        
        return response()->json(['deleted' => true  ]);        
    }

    
	
}
