<?php

namespace App\Http\Middleware;

use Closure;

class CheckOwner
{
    protected $user;
   

    public function getOwner() {
        return   $this->user;
    }
    public function adminId() {
        return   $this->user->id;
    }
    public function canAdminCenter($center_id) {
        return $this->user->admin->centers->pluck('id')->contains($center_id);
    }
    public function centersCanAdmin()
    {
         return $this->user->admin->validCenters();
    }

    public function handle($request, Closure $next)
    {
        $user=request()->user();
        if(!$user){
            return   response()
                    ->json(['msg' => '權限不足'
                        ]  ,  401);
        }  
        if(!$user->isOwner()){
             return   response()
                    ->json(['msg' => '權限不足'
                        ]  ,  401);
        }

        $user->admin->centers;
        $this->user=$user;
       
      
        return $next($request);
    }
}
