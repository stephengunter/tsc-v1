<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Repositories\Users;
use App\Repositories\Centers;

class UsersCentersController extends Controller
{
    public function __construct(Users $users,Centers $centers) {
        $this->users=$users;
        $this->centers=$centers;
	}

    public function index()
    {
         $userId=request('user');
         $user =$this->users->findOrFail($userId);
         $user->centers;

             return response()
                ->json($user);
       
    }
    public function create()
    {
        $userId=request('user');
        $user =$this->users->findOrFail($userId);
        $centersCanAdd=$user->centersCanAdd();
        $centerOptions=$this->centers->optionsConverting($centersCanAdd);
        
       
        return response()->json($centerOptions);
            
    }
    
    public function store(Request $request)
    {
        $user_id=$request['user_id'];
        $center_id=$request['center_id'];
        $this->centers->attachUser($user_id,$center_id);
        
        return response()
                ->json([
                    'saved' => true
                ]);
      
    }
    // public function show($id)
    // {
    //     $center=$this->centers->findOrFail($id);
    //      return response()
    //             ->json([
    //                 'center' => $center
    //             ]);
       
    // }
    public function remove(Request $request)
    {
         $user_id=$request['user_id'];
         $center_id=$request['center_id'];
         $this->centers->detachUser($user_id,$center_id);
        
            return response()
                    ->json([
                        'saved' => true
                    ]);
    }
    
}
