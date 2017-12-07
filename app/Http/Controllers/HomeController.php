<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Repositories\Teachers;
use Illuminate\Http\Request;
use Hash;
use Auth;
use App\User;

class HomeController extends BaseController
{
    
    public function __construct(Teachers $teachers)                                   
    {
        $this->teachers=$teachers;

    }
    public function index()
    {  
        if(!request()->ajax()){
            return view('app');
        }

        $current_user=$this->currentUser(); 

        // $keys=[ 
        //         'signups','refunds',
        //         'courses','students',
        //         'users','teachers',
        //         'main_settings','settings',
        //         'discounts',
        //         'notices','reports'];
                $keys=[ 
                    'signups',
                    'students',
                    'courses',
                    'credit_courses',
                    'teachers',
                    'main_settings',
                    'reports'
                   ];        
                
        if($current_user->isOwner() ||  $current_user->isDev()){
            array_push($keys, 'admins');
        }
        $systems=[];
        for( $i=0; $i < count($keys); $i++ ){
           $menus=array(
             $keys[$i] => $this->menus($keys[$i])
           );
           $systems = array_merge($systems,$menus);
           
        }

        $badges=[
            'unreviewTeachers' => $this->unreviewTeachers()
        ];
        return response()
            ->json([
                'model' => $systems,
                'badges' => $badges
            ]);
    }

    private function unreviewTeachers()
    {
        $admin_centers=$this->canAdminCenters();

        if(!$admin_centers) return 0;

        $admin_center_ids=$admin_centers->pluck('id')->toArray();
        if(!count($admin_center_ids)) return 0;

        return $this->teachers->getByCenters($admin_center_ids)
                        ->where('reviewed',false)->count();
       
        
    }
}
