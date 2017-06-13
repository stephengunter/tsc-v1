<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Repositories\Teachers;
use App\Repositories\Centers;

use App\Support\Helper;
use App\Http\Middleware\CheckAdmin;

class CenterTeacherController extends Controller
{
    public function __construct(Teachers $teachers,Centers $centers,
                                CheckAdmin $checkAdmin)
    {
        
        $this->middleware('admin');
        $this->checkAdmin=$checkAdmin;

        $this->teachers=$teachers;
        $this->centers=$centers;
	}

    public function index()
    {
         $teacherId=request('teacher');
         $teacher =$this->teachers->findOrFail($teacherId);
         $teacher->centers;

         $current_user=$this->checkAdmin->getAdmin();

         $teacher->centersCanAdd=$teacher->centersCanAddByAdmin($current_user->admin);
         
         $validCenters=$teacher->validCenters();
         foreach($validCenters as $center)
         {
             $center->canDelete=$center->canDeleteBy($current_user);
         }
         $teacher->centers=$validCenters;
         

         return response()->json([
                'teacher' => $teacher
            ]); 
       
    }
   
    public function store(Request $request)
    {
        $teacher_id=$request['teacher_id'];
        $center_id=$request['center_id'];
        $canAdminCenter=$this->checkAdmin->canAdminCenter($center_id);
        if(!$canAdminCenter)
        {
           return   response()->json(['msg' => '權限不足' ]  ,  401); 
        }

        $this->teachers->attachCenter($teacher_id,$center_id);
        
        
        return response()
                ->json([
                    'saved' => true
                ]);
      
    }
    
    public function remove(Request $request)
    {
         $teacher_id=$request['teacher_id'];
         $center_id=$request['center_id'];
         $canAdminCenter=$this->checkAdmin->canAdminCenter($center_id);
         if(!$canAdminCenter)
         {
            return   response()->json(['msg' => '權限不足' ]  ,  401); 
         }
         $this->teachers->detachCenter($teacher_id,$center_id);
        
            return response()
                    ->json([
                        'saved' => true
                    ]);
    }
    
}
