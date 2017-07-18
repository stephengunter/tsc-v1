<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;

use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;

use App\Repositories\Users;
use Illuminate\Support\Facades\Hash;
use App\Jobs\SendResetPasswordMail;

use App\User;


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

    public function forgot(ForgotPasswordRequest $request)
    {
        $email=$request['email'];
        $user=$this->users->findByEmail($email);
        
        if($user){
            dispatch(new SendResetPasswordMail($user));          
        }
        return response()->json(['done' => true]);              
                    
    }

    public function reset(ResetPasswordRequest $request)
    {
        abort(404);
        
        $user_id=$request['user_id'];
        $token=$request['token'];
        $email=$request['email'];

        $user=$this->checkResetPasswordToken($user_id, $token, $email);

        if(!$user) abort(404);
             
        $user->password = $request['password'];
        $user->save(); 

        return response()->json($user);   
    }

    private function checkResetPasswordToken($user_id, $token, $email)
    {
        $user= User::findOrFail($user_id);
        if($user->email!=$email)  return null;

        if(!$user->passwordResetToken) return null;
        if(!$user->passwordResetToken->isValid()) return null;

        if($token!=$user->passwordResetToken->token) return null;

        return $user;

    }

    
}
