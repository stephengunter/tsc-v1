<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Controllers\Controller;
use Auth;

class SessionsController extends Controller
{
    public function __construct() {
		$this->middleware('guest', ['except' => 'destroy']);
	}

    public function create()
    {  
        return view('auth.login');
    }

	public function store(LoginRequest $request)
    {  
		// if (!filter_var($userValues['email'], FILTER_VALIDATE_EMAIL)) {
               
        // }
        
		$values=[
			'email' => $request['username'],
			'password' => $request['password'],
		];
	
		if (!Auth::attempt($values)) {
            return   response()->json(['msg' => '登入失敗' ]  ,  422);
		}

		if(!Auth::user()->email_confirmed){
			Auth::logout();
			return response()->json(['error' => 'email unconfirmed'], 439);
		}
		
		return response()->json(['success' => true]);         
                
	}

	public function destroy() {
		auth()->logout();
        if(!request()->ajax())  return redirect('login');
		return response()
                ->json([
                    'success' => true
                ]);
	}
}
