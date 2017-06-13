<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\BaseController;

use App\Http\Middleware\CheckAdmin;
use Illuminate\Http\Request;

use App\User;
use App\Profile;

use App\Repositories\Users;

use App\Support\Helper;


class UserRolesController extends BaseController
 {
    
    public function __construct(Users $users, CheckAdmin $checkAdmin)
    {
          $exceptAdmin=[];
          $allowVisitors=[];
        
		  $this->setMiddleware($exceptAdmin, $allowVisitors);
        
        
          $this->users=$users;
         
          $this->setCheckAdmin($checkAdmin);
          
	}

	
    public function index()
    {
        $request = request();
        $user_id=(int)$request->user; 
        $user =$this->users->findOrFail($user_id);
        
         return response()
            ->json([
                'roles' => $user->roles
            ]);
    }
   

    public function edit($id)
    {
        $user =$this->users->findOrFail($id);
        
        $with_admin=$this->checkAdmin->isOwner();
         return response()
            ->json([
                'roles' => $user->rolesCanAdd($with_admin)
            ]);
        
    }

    public function store(UserRequest $request)
    {
        $admin_id=$this->checkAdmin->getAdminId();

        $removed=false;
        $updated_by=$admin_id;

        $userValues=$request->getUserValues($updated_by,$removed);

        $email_token=$this->registrations->createToken();
        $userValues=array_add($userValues, 'email_token', $email_token);

        $profileValues=$request->getProfileValues($updated_by);
        
        $user=$this->users->store($userValues, $profileValues);

       
        dispatch(new SendEmailConfirmationMail($user));
     

        return response()
            ->json([
                'user' => $user
            ]);
    }

    

    public function update(UserRequest $request, $id)
    {
        
         $current_user=request()->user();        
         $user = User::findOrFail($id);
         if(!$user->canEditBy($current_user)){
            return   response()->json(['msg' => '權限不足' ]  ,  401);      
         }
         $updated_by=$current_user->id;
         $removed=false;
         $userValues=$request->getUserValues($updated_by,$removed);       
         $profileValues=$request->getProfileValues($updated_by);
         $user= $this->users->updateUserAndProfile($userValues,$profileValues, $user);
        
         return response()->json($user);
    }

    public function destroy($id)
    {
        $current_user=$this->checkAdmin->getAdmin();
        
        $deleted=$this->users->delete($id ,$current_user);
        if(!$deleted){
            return   response()->json(['msg' => '權限不足' ]  ,  401); 
        }

        return response()
            ->json([
                'deleted' => true
            ]);
    }
   
    public function rolesCanAdd($id)
    {
        $user =$this->users->findOrFail($id);
        
        $with_admin=$this->checkAdmin->isOwner();
         return response()
            ->json([
                'roles' => $user->rolesCanAdd($with_admin)
            ]);
    }
    
    

    
	
}
