<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\BaseController;

use App\Http\Requests\Teacher\TeacherRequest;
use Illuminate\Http\Request;

use App\Teacher;
use App\User;
use App\Profile;

use App\Repositories\Teachers;
use App\Repositories\Courses;
use App\Repositories\Users;
use App\Repositories\Centers;
use App\Repositories\Roles;
use App\Http\Middleware\CheckAdmin;
use App\Support\Helper;

use DB;
use App\Events\TeacherCreated;
use App\Events\TeacherDeleted;

class GroupTeachersController extends BaseController
 {
   

    public function __construct(Teachers $teachers ,Users $users, Courses $courses,
                                Centers $centers, CheckAdmin $checkAdmin)                               
    {
        //  $exceptAdmin=['create','edit','store','show','edit','update','updateUser'];
		$exceptAdmin=[];
        $allowVisitors=[];

        $this->setMiddleware( $exceptAdmin, $allowVisitors);
        
		$this->teachers=$teachers;       
        $this->users=$users;  
        $this->courses=$courses; 
        $this->centers=$centers; 

        $this->setCheckAdmin($checkAdmin);
         
	}
    public function show($id)
    {
        $teachers=[];
        
        $teachers=$this->teachers->groupTeachers($id)->get();
       
        if(count($teachers)){
            foreach($teachers as $teacher){
                $teacher->centerNames=$teacher->centerNames();
            }
        }
        return response()->json([ 'teachers' => $teachers ]); 
    }
    public function store(Request $request)
    {
         $group_id=$request['group_id'];
         $teacher_id=$request['teacher_id'];
         if(!$group_id) abort(500);
         if(!$teacher_id) abort(500);
         
         $teacher_group=$this->teachers->findOrFail($group_id);        
         if(!$teacher_group->teacher_ids) {
            $teacher_group->teacher_ids=$teacher_id;
            $teacher_group->save();
            return response()->json([ 'success' => true ]);
         }

         $teacher_ids_array= explode( ',', $teacher_group->teacher_ids );
         if(!in_array($teacher_id, $teacher_ids_array)){
            $teacher_group->teacher_ids .= ',' .  $teacher_id;
            $teacher_group->save();
            
         }
         return response()->json([ 'success' => true ]);
    
              
         
    }


    public function remove(Request $request,$id)
    {
        $removed_id=$request['remove_id'];      
        if(!$removed_id) abort(500);

        $teacher_group=$this->teachers->findOrFail($id);        
        if(!$teacher_group->teacher_ids) abort(500);
       
        $teacher_ids_array=explode( ',', $teacher_group->teacher_ids );
        $teacher_ids_array=Helper::removeValueFromArray($teacher_ids_array,$removed_id);       
       
       
        $teacher_group->teacher_ids = Helper::strFromArray($teacher_ids_array);       
        $teacher_group->save();

        return response()->json([ 'success' => true ]);
    }
   

    

    

	
}
