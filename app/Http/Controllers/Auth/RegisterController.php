<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;

use App\Repositories\Registrations;
use App\Repositories\Users;
use App\Jobs\SendEmailConfirmationMail;

class RegisterController extends BaseController
{
    public function __construct(Registrations $registrations, Users $users)
    {
          $this->middleware('guest');

          $this->registrations=$registrations;
          $this->users=$users;
          
	}


    public function store(RegisterRequest $request)
    {
        $userValues=$request->getValues();  
       
        $user=$this->registrations->register($userValues);
        
        return response()->json($user);
           
    }

    public function confirmEmail(Request $request)
    {
        $values=$request->user;
        $user_id=$values['id'];
        $token=$values['token'];
       
        $confirm=$this->registrations->confirmEmail($user_id, $token);

        if(!$confirm) abort(404);

        return   response()->json(['confirmed' => true ]);

    }
    public function sendConfirmationMail(Request $request)
    {
        $email=$request->email;
        $user=$this->users->findByEmail($email);
        if(!$user) abort(404);

        
        dispatch(new SendEmailConfirmationMail($user));

        return   response()->json(['send' => true ]);

    }
    
    
}
