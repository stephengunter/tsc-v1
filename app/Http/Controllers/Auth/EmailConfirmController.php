<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;

use App\Repositories\Registrations;
use App\Repositories\Users;
use App\Jobs\SendEmailConfirmationMail;



class EmailConfirmController extends BaseController
{
    public function __construct(Registrations $registrations, Users $users)
    {
          $this->middleware('guest');

          $this->registrations=$registrations;
          $this->users=$users;
          
	}
    public function create($email)
    {
        return view('auth.email.unconfirmed')->with(['email'=>$email]);
    }

    public function confirmEmail($user,$token)
    {
      
        $confirm=$this->registrations->confirmEmail($user, $token);
        if($confirm){
             return view('auth.email.confirmed')->with(['confirmed'=> true]);
        }else{
            return view('auth.email.confirmed');
        }

    }
    public function sendMail(Request $request)
    {
        $email=$request->email;
        $user=$this->users->findByEmail($email);
        if(!$user) abort(404);
        
        dispatch(new SendEmailConfirmationMail($user));
        
        return   response()->json(['send' => true ]);
    }
    
    
}
