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

    public function create()
    {
         $request = request();
         $group_teacher_id=(int)$request->id;

        $centerId=(int)$request->center;
        $teacherList=[];
        if($centerId){
          
            $teacherList=$this->teachers->getByCenter($centerId)
                                        ->with('user.profile');
                                       
        }else{
             $teacherList=$this->teachers->getAll()
                                        ->with('user.profile');
        }

        $teacherList=$teacherList->filterPaginateOrder();   
        if(count($teacherList)){
            foreach($teacherList as $teacher){
                $teacher->centerNames=$teacher->centerNames();
            }
        }
                                    
        return response()
            ->json([
               'model' => $teacherList                
            ]);
    }

   

    public function edit($id)
    {
        $current_user=request()->user();
        $teacher=$this->teachers->findOrFail($id);
        if(!$teacher->canEditBy($current_user)){
            return  $this->unauthorized();    
        }

        $teacher->name=$teacher->getName();
        return response()->json([
            'teacher' => $teacher
        ]);
        
    }
    

    
    public function update(TeacherRequest $request,$id)
    {
        $current_user=$this->currentUser();
        $teacher = Teacher::findOrFail($id);
        if(!$teacher->canEditBy($current_user)){
            return  $this->unauthorized();    
        }

        $updated_by=$current_user->id;
        $removed=false;
        $teacherValues=$request->getTeacherValues($updated_by,$removed);

        if(!$teacher->reviewed){
            if((int)$teacherValues['reviewed'] > 0){
                $teacherValues['reviewed_by']= $updated_by;
            }
        }
        
        $teacher->update($teacherValues);
        
       
        return response()->json($teacher); 
         
           
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
