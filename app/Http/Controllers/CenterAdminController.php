<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Repositories\Admins;
use App\Repositories\Centers;

use App\Http\Middleware\CheckOwner;

class CenterAdminController extends Controller
{
    public function __construct(Admins $admins,Centers $centers,
                            CheckOwner $checkOwner) 
    {
        $this->middleware('owner');

        $this->admins=$admins;
        $this->centers=$centers;

        $this->checkOwner=$checkOwner;
	}

    public function index()
    {
         $id=request('admin');
         $admin =$this->admins->findOrFail($id);
         $current_user=$this->checkOwner->getOwner();
         
         $admin->centersCanAdd=$admin->centersCanAddByAdmin($current_user->admin);
         
         $validCenters=$admin->validCenters();
         if(count($validCenters))
         {
            foreach($validCenters as $center)
            {
                $center->canDelete=$center->canDeleteBy($current_user);
            }
         }
         

         $admin->centers=$validCenters;

             return response()
                ->json(['admin'=> $admin]);

       
    }
    
    public function store(Request $request)
    {
        $admin_id=$request['admin_id'];        
        $center_id=$request['center_id'];
        $canAdminCenter=$this->checkOwner->canAdminCenter($center_id);
        if(!$canAdminCenter)
        {
           return   response()->json(['msg' => '權限不足' ]  ,  401); 
        }

        $this->admins->attachCenter($admin_id,$center_id);
        
        
        return response()
                ->json([
                    'saved' => true
                ]);
      
    }
    
    public function remove(Request $request)
    {
         $admin_id=$request['admin_id'];
       
         $center_id=$request['center_id'];
         $canAdminCenter=$this->checkOwner->canAdminCenter($center_id);
         if(!$canAdminCenter)
         {
           return   response()->json(['msg' => '權限不足' ]  ,  401); 
         }
         $this->admins->detachCenter($admin_id,$center_id);
        
            return response()
                    ->json([
                        'saved' => true
                    ]);
    }
    
}
