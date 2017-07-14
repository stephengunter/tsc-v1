<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;

use App\Http\Requests\Auth\ChangePasswordRequest;

use App\Repositories\Users;
use Illuminate\Support\Facades\Hash;
use App\Http\Middleware\CheckAdmin;
use App\Jobs\SendResetPasswordMail;

use App\User;

use App\Http\Controllers\Controller;


class PasswordsController extends BaseController
{
    public function __construct(Users $users) 
    {
		
        $this->users=$users;
          
    }
    

    public function change(ChangePasswordRequest $request)
    {
        $current_user=$this->currentUser();          
        if(Hash::check($request['current_password'], $current_user->password))
        {           
            $user = $this->users->findOrFail($current_user->id);  
            $user->password = $request['password'];
            $user->save(); 
            return response()->json($user);   
        }
        else
        {     
            return response()->json([ 'current_password' => ['密碼錯誤']]  ,  422);
        }
    }

    
}
