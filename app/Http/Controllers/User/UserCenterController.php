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
    
    
    public function __construct(Users $users,Centers $centers, CheckAdmin $checkAdmin)
    {
        $exceptAdmin=[];
        $allowVisitors=[];

        $this->setMiddleware( $exceptAdmin, $allowVisitors);
        
        $this->users=$users;
        $this->centers=$centers;
         

        $this->setCheckAdmin($checkAdmin);
          
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
            $centers=$user->teacher->validCenters();
        }else if( $role == strtolower(Role::volunteerRoleName()) ){
            $centers=$user->volunteer->validCenters();
        }else if(in_array($role, Role::adminRoleNames(true))){
            $centers=$user->admin->validCenters();
        }
       
        if(count($centers)){
            foreach($centers as $center)
            {
                $center->canDelete=$center->canDeleteBy($current_user);
            }
            
        }
        

        return response()->json(['centers' => $centers ]);
       
    }
    public function create()
    {
        $current_admin=$this->currentUser()->admin;
        $request = request();
        
        $role=$request->role; 
        $user_id=(int)$request->user;

        if(!$role || !$user_id) abort(404);
        $user =$this->users->findOrFail($user_id);

        $role=strtolower($role);
        $options=[];
        $centersCanAdd=[];
       
        if( $role == strtolower(Role::teacherRoleName()) ){
            $centersCanAdd=$user->teacher->centersCanAddByAdmin($current_admin);
        }else if( $role == strtolower(Role::volunteerRoleName()) ){
            $centersCanAdd=$user->volunteer->centersCanAddByAdmin($current_admin);
        }else if(in_array($role, Role::adminRoleNames(true))){
            $centersCanAdd=$user->admin->centersCanAddByAdmin($current_admin);
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
            $user->admin->attachCenter($center_id);
        }else{
            abort(404);
        }
        
        return response()->json([ 'saved' => true ]);   
      
    }
   
    public function destroy($id)
    {
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
            $admin=Admin::findOrFail($id);
            $admin->detachCenter($center_id);
        }else{
            abort(404);
        }
        
        return response()->json(['deleted' => true  ]);        
    }

    
	
}
