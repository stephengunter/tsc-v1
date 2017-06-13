<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Requests\Auth\ForgotPasswordRequest;

use App\Repositories\Users;

use App\Jobs\SendResetPasswordMail;

use App\Http\Controllers\Controller;

class ForgotPasswordController extends Controller
{
    public function __construct(Users $users) 
    {
		
        $this->middleware('guest'); 
        $this->users=$users;
          
	}

    public function index()
    {
        return view('auth.passwords.forgot');
    }
    

    public function store(ForgotPasswordRequest $request)
    {
        $email=$request['email'];
        $user=$this->users->findByEmail($email);
        $frontend=false;
        
        if($user){
            dispatch(new SendResetPasswordMail($user,$frontend));          
        }
        return response()
                    ->json([
                        'done' => true
                    ]);
    }
     
}
