<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Controllers\Controller;
use App\Http\Middleware\CheckAdmin;
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

		$user=Auth::user();
		$can_login= CheckAdmin::canLogin($user);

		if($can_login){
            return response()->json(['success' => true]);  
        }else{

            return CheckAdmin::exceptions($user);

        }     
                
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
