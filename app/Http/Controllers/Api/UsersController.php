<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\User;
use App\Photo;
use App\Repositories\Users;

use App\Http\Requests\User\UserRequest; 

use App\Support\Helper;

use App\Events\CourseUpdated;
use Carbon\Carbon;




class UsersController extends BaseController
{
  
    public function __construct(Users $users)                     
    {
        $this->middleware('auth:api');
        $this->users=$users;

	}
    public function edit($id)
    {
        $current_user=$this->currentUser();
        $user = User::findOrFail($id);
        if(!$user->canEditBy($current_user)){
            return  $this->unauthorized();       
        }

        $user->profile->photo=$user->profile->photo();

        return response()->json(['user' => $user]);  
        
    }
    public function show($id)
    {
        $current_user=$this->currentUser();
        $user = User::findOrFail($id);
        if(!$user->canEditBy($current_user)){
            return  $this->unauthorized();       
        }

        $user->getPhoto();
     
        return response()->json(['user' => $user]);        
        
    }

    public function update(UserRequest $request, $id)
    {
        
        $current_user=$this->currentUser();      
        $user = User::findOrFail($id);
        if(!$user->canEditBy($current_user)){
            return  $this->unauthorized();       
        }


        $updated_by=$current_user->id;
        $removed=false;
        $userValues=$request->getUserValues($updated_by,$removed);       
        $profileValues=$request->getProfileValues($updated_by);

    
        $user= $this->users->updateUserAndProfile($userValues,$profileValues, $user);

        
        
        $user->getPhoto();
        return response()->json($user);
    }

    public function updatePhoto(Request $request, $id)
    {
        $user =$this->users->findOrFail($id);

        $current_user=$this->currentUser(); 
        if(!$user->canEditBy($current_user)){
              return  $this->unauthorized();
        }
       
        $user->profile->photo_id=$request['photo_id'];
        $user->profile->updated_by=$current_user->id;
        $user->profile->save();

        return response()->json([ 'saved' => true ]);         

    }
    
}
