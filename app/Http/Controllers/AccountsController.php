<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Accounts;
use App\Repositories\Users;

use App\Support\Helper;
use App\Http\Middleware\CheckAdmin;

class AccountsController extends Controller
{
     public function __construct(Accounts $accounts,Users $users,CheckAdmin $checkAdmin)                                          
     {
         $exceptAdmin=['getByUser'];
         $allowVisitors=[];
		 $this->middleware('admin', ['except' => array_merge($exceptAdmin,$allowVisitors) ]);
         $this->middleware('auth', ['only' => $exceptAdmin]);

		
         $this->accounts=$accounts;
         $this->users=$users;

         $this->checkAdmin=$checkAdmin;

	 }

     public function store(Request $request)
     {
         
     }

     public function getByUser($user)
     {
         $current_user=request()->user();
         $userData=$this->users->findOrFail($user);
         if(!$userData->canViewBy($current_user)){
           return   response()->json(['msg' => '權限不足' ]  ,  401);    
         }
         $accountList=$this->accounts->getByUser($user)->get();
           return response()
            ->json([
                'accountList' => $accountList
            ]);
     }
}
