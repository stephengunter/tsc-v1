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
	public function index()
    {
        $teachers=[];
        $request=request();
        $id=(int)$request->id;

        if(!$id)  return response()->json([ 'teachers' => $teachers ]); 
        
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
         $user_id=(int)$request->user;
         
         if(!$request->ajax()){
            $menus=$this->menus($this->key);            
            return view('teachers.create')
                   ->with([ 'menus' => $menus,
                              'id' => $user_id     
                        ]);
         }  

         $user=null;
         $teacher=null;

         if($user_id){
            $current_user=$this->currentUser();
            $user=$this->users->findOrFail($user_id);
            if(!$user->canViewBy($current_user)){
                return  $this->unauthorized();     
            }

            
            $user->profile;
            $teacher=$user->teacher;
            if(!$teacher){
                 $teacher=$this->teachers->initialize();
            }
           
         }else{
             $user=User::initialize();
             $teacher=$this->teachers->initialize();
         }

          return response()->json([
                'user' => $user,
                'teacher' => $teacher
            ]);
    }

    public function show($id)
    {
        

        if(!request()->ajax()){
            $menus=$this->menus($this->key);            
            return view('teachers.details')
                    ->with([ 'menus' => $menus,
                              'id' => $id     
                        ]);
        }  

        $current_user=$this->currentUser();
        $teacher=$this->teachers->findOrFail($id);
        if(!$teacher->canViewBy($current_user)){
           return  $this->unauthorized();  
        }
        $teacher->name=$teacher->getName();
        $teacher->canEdit=$teacher->canEditBy($current_user);
        $teacher->canDelete=$teacher->canDeleteBy($current_user);
        

         return response()
                ->json([
                    'teacher' => $teacher
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

    public function destroy($id)
    {
        $removed_id=$request['teacher_id'];
        if(!$removed_id) abort(500);
        $teacher_group=$this->teachers->findOrFail($id);
        if(!$teacher_group->teacher_ids) abort(500);
       
        $teacher_ids_array=explode( ',', $teacher_group->teacher_ids );
       
        $teacher_ids_array=Helper::removeValueFromArray($teacher_ids_array,$removed_id);
        $teacher_group->teacher_ids = Helper::strFromArray($teacher_ids_array);
        $teacher_group->save();

        return response()->json([ 'success' => true ]);
    }
   

    public function options()
    {
        $request=request();
        $options=[];
        $centerId=(int)$request->center;
        if($centerId){
            $options=$this->teachers->optionsByCenter($centerId);
            return response()->json([ 'options' => $options ]);  
        }

        $courseId=(int)$request->course;
        if($courseId){
            $course=$this->courses->findOrFail($courseId);
            $options=$this->teachers->optionsConverting($course->teachers);
            return response()->json([ 'options' => $options ]);  
        }

        $teachers=$this->teachers->getAll()->get();
        $options=$this->teachers->optionsConverting($teachers);
        return response()->json([ 'options' => $options ]);  
          
        
    }

    

    

	
}
