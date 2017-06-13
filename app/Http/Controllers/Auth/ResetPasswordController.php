<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Requests\Auth\ResetPasswordRequest;

use App\Repositories\Users;
use Illuminate\Support\Facades\Hash;
use App\Http\Middleware\CheckAdmin;

use App\User;

use App\Http\Controllers\Controller;

class ResetPasswordController extends Controller
{
    public function __construct(Users $users) 
    {
        $this->users=$users;
          
	}

    public function create($user,$token)
    {
        
        return view('auth.passwords.reset')->with([
                            'user_id' =>  $user,
                            'token' => $token,
                        ]);
    }

    
    public function store(ResetPasswordRequest $request)
    {
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
