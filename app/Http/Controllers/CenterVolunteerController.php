<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Repositories\Volunteers;
use App\Repositories\Centers;


use App\Support\Helper;
use App\Http\Middleware\CheckAdmin;

class CenterVolunteerController extends Controller
{
    public function __construct(Volunteers $volunteers,Centers $centers,CheckAdmin $checkAdmin)
    {
         $this->middleware('admin');
         $this->checkAdmin=$checkAdmin;
         $this->volunteers=$volunteers;
         $this->centers=$centers;
	}

    public function index()
    {
         $id=request('volunteer');
         $volunteer =$this->volunteers->findOrFail($id);
         $volunteer->centers;

         $current_user=$this->checkAdmin->getAdmin();
         $volunteer->centersCanAdd=$volunteer->centersCanAddByAdmin($current_user->admin);
         
         foreach($volunteer->centers as $center)
         {
            $center->canDelete=$center->canEditBy($current_user);
         }

         return response()
              ->json(['volunteer'=> $volunteer]);

       
    }
    public function store(Request $request)
    {
        $volunteer_id=$request['volunteer_id'];
        $center_id=$request['center_id'];
        $canAdminCenter=$this->checkAdmin->canAdminCenter($center_id);
        if(!$canAdminCenter)
        {
           return   response()->json(['msg' => '權限不足' ]  ,  401); 
        }

        $this->volunteers->attachCenter($volunteer_id,$center_id);
        
        
        return response()
                ->json([
                    'saved' => true
                ]);
      
    }
    
    public function remove(Request $request)
    {
         $volunteer_id=$request['volunteer_id'];
         $center_id=$request['center_id'];
         $canAdminCenter=$this->checkAdmin->canAdminCenter($center_id);
         if(!$canAdminCenter)
         {
            return   response()->json(['msg' => '權限不足' ]  ,  401); 
         }
         $this->volunteers->detachCenter($volunteer_id,$center_id);
        
            return response()
                    ->json([
                        'saved' => true
                    ]);
    }
    
}
