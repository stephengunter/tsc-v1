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
    protected $key='teachers';

    public function __construct(Teachers $teachers ,Users $users, Courses $courses, Centers $centers)                                                              
    {
		$this->teachers=$teachers;       
        $this->users=$users;  
        $this->courses=$courses; 
        $this->centers=$centers; 
         
    }
    public function show($id)
    {
        $teachers=$this->teachers->teachersInGroup($id);
        if(!$teachers || !count($teachers))  return response()->json([ 'teachers' => [] ]); 

        $teachers=$teachers->get();
        if(!count($teachers))  return response()->json([ 'teachers' => [] ]);

        foreach($teachers as $teacher){
            $teacher->centerNames=$teacher->centerNames();
        }
        return response()->json([ 'teachers' => $teachers ]); 
    }
    public function create()
    {
        $request = request();

        $centerId=(int)$request->center;
        
       
        $teacherList=[];
        if($centerId){
            $teacherList=$this->teachers->getByCenter($centerId) ->with('user.profile');                                      
                                       
        }else{
            $teacherList=$this->teachers->getAll()->with('user.profile');                                        
        }

        $teacherList=$teacherList->where('group',false);
        
        $teacherGroupId=(int)$request->group_id;            
        if($teacherGroupId){
            $teacherGroup=$this->teachers->findOrFail($teacherGroupId);
            $teacherIds=$teacherGroup->groupTeacherIds();           
            array_push($teacherIds,$teacherGroupId);
        
        
            $teacherList=$teacherList
                        ->where('group',false)
                        ->whereNotIn('user_id',$teacherIds);
        }

        

        $teacherList=$teacherList->filterPaginateOrder();   
        if(count($teacherList)){
            foreach($teacherList as $teacher){
                $teacher->centerNames=$teacher->centerNames();
            }
        }        
                                    
        return response()->json([ 'model' => $teacherList ]);
       
    }
    public function store(Request $request)
    {
        $group_id=$request['group_id'];
        $teacher_id=$request['teacher_id'];
        if(!$group_id) abort(500);
        if(!$teacher_id) abort(500);
        
        $current_user=$this->currentUser();

        $teacher_group=$this->teachers->findOrFail($group_id);   
        if(!$teacher_group->canEditBy($current_user)){
            return  $this->unauthorized();    
        }  
        
        $teacher=$this->teachers->findOrFail($teacher_id);   

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

        $center_ids=$teacher_group->centers->pluck('id')->toArray();
        if(count($center_ids)){
            foreach($center_ids as $center_id){
                $teacher->attachCenter($center_id);
            } 
        }
        
        return response()->json([ 'success' => true ]);
    
              
         
    }


    public function remove(Request $request,$id)
    {
        $removed_id=$request['remove_id'];      
        if(!$removed_id) abort(500);

        $teacher_group=$this->teachers->findOrFail($id);        
        if(!$teacher_group->teacher_ids) abort(500);

        $current_user=$this->currentUser();
        if(!$teacher_group->canEditBy($current_user)){
            return  $this->unauthorized();    
        }  
       
        $teacher_ids_array=explode( ',', $teacher_group->teacher_ids );
        $teacher_ids_array=Helper::removeValueFromArray($teacher_ids_array,$removed_id);       
       
       
        $teacher_group->teacher_ids = Helper::strFromArray($teacher_ids_array);       
        $teacher_group->save();

        return response()->json([ 'success' => true ]);
    }
   

    

    

	
}
