<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

use App\Http\Requests\Auth\ChangePasswordRequest;

use App\Repositories\Users;
use Illuminate\Support\Facades\Hash;
use App\Http\Middleware\CheckAdmin;
use App\Jobs\SendResetPasswordMail;

use App\User;

use App\Http\Controllers\Controller;


class ChangePasswordController extends BaseController
{
    public function __construct(Users $users) 
    {
		$this->middleware('auth');
        $this->users=$users;
          
    }

    public function index()
    {
        return view('auth.passwords.change');
    }

    public function store(ChangePasswordRequest $request)
    {
       
        $current_user=$this->currentUser();
        $current_password = $current_user->password;           
        if(Hash::check($request['current_password'], $current_password))
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
