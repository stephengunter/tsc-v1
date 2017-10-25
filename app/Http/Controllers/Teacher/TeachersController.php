<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\BaseController;

use App\Http\Requests\Teacher\TeacherRequest;
use Illuminate\Http\Request;

use App\Teacher;
use App\User;
use App\Profile;
use App\Address;

use App\Repositories\Teachers;
use App\Repositories\Courses;
use App\Repositories\Users;
use App\Repositories\Centers;
use App\Repositories\Roles;
use App\Http\Middleware\CheckAdmin;
use App\Support\Helper;

use Illuminate\Support\Facades\Input;

use DB;

use Storage;

class TeachersController extends BaseController
 {
    protected $key='teachers';

    public function __construct(Teachers $teachers ,Users $users, Courses $courses,
                                Centers $centers)                               
    {
        
        
        $this->teachers=$teachers;
           
        $this->users=$users;  
        $this->courses=$courses; 
        $this->centers=$centers; 

       
         
	}
	public function index()
    {
       
        if(!request()->ajax()){
            $menus=$this->menus($this->key);            
            return view('teachers.index')
                    ->with(['menus' => $menus]);
        }  
        $request = request();
        
        $centerId=(int)$request->center;
        $group_teacher_id=(int)$request->group;
        $teacherList=[];
        if($centerId){
          
            $teacherList=$this->teachers->getByCenter($centerId)
                                        ->with('user.profile');
                                       
        }else{
            $teacherList=$this->teachers->getAll()
                                        ->with('user.profile');
        }

        if($group_teacher_id){
            $group_teacher=$this->teachers->findOrFail($group_teacher_id);
            $groupTeacherIds=$group_teacher->groupTeacherIds();           
            array_push($groupTeacherIds,$group_teacher_id);
           
          
            $teacherList=$teacherList
                        ->where('group',false)
                        ->whereNotIn('user_id',$groupTeacherIds);
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
        $teacher->canReview=$teacher->canReviewBy($current_user);
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
    
    public function store(TeacherRequest $request)
    {
       
        $current_user=$this->currentUser();
        $updated_by=$current_user->id;
        $removed=false;

        
        $teacher=null;
        if($request->isGroup()){
            $teacherValues=$request->get('teacher');
            $name=$teacherValues['name'];
            $description=$teacherValues['description'];

            $teacher=$this->teachers->storeTeacherGroup($name,$description,$current_user);
        }else{

            $teacherValues=$request->getTeacherValues($updated_by,$removed);
            
            $userValues=$request->getUserValues($updated_by,$removed);
            $profileValues=$request->getProfileValues($updated_by,$removed);
    
            $user_id=$request->getUserId();
            $teacherId=$request->getTeacherId();
    
            $teacher=$this->teachers->storeTeacher($userValues,$profileValues,$teacherValues,$user_id,$teacherId,$current_user);
    
           
        }

        
       
        return response()->json($teacher); 
            
    }

    
    public function import()
    {
        

        $menus=$this->menus($this->key);     
       
        return view('teachers.import')
                ->with(['menus' => $menus]);
    }
    public function importTeachers(Request $form)
    {

        if(!$form->hasFile('teachers_file')){
            return   response()
                        ->json(['teachers_file' => ['無法取得上傳檔案'] 
                            ]  ,  422);      
        }

        $current_user=$this->currentUser();

        $file=Input::file('teachers_file');
        $type=(int)$form['type'];

       

        if($type){
            $this->teachers->importTeachers($file,$current_user);
           
        }else{
            $this->teachers->importGroupTeachers($file,$current_user);
        }   

        return response()->json(['success' => true]);

       
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
      
        if(!$teacher->canReviewBy($current_user)){
            $teacherValues['reviewed'] = 0;
            $teacherValues['reviewed_by'] = '';
        }
        
        $teacher->update($teacherValues);
        
       
        return response()->json($teacher); 
         
           
    }
    public function updateReview(Request $form)
    {
        $id=$form['id'];
        $reviewed=$form['reviewed'];

        $current_user=$this->currentUser();
       
        $teacher=$this->teachers->updateReview($id,$reviewed,$current_user);    

        return response()->json($teacher); 

        
    }

    public function destroy($id)
    {
        $teacher=$this->teachers->findOrFail($id);
        $current_user=$this->currentUser();
        if(!$teacher->canDeleteBy($current_user)){
            return  $this->unauthorized();     
        }
       
        $this->teachers->delete($id, $current_user);

        return response()
            ->json([
                'deleted' => true
            ]);
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

    public function groupTeachers()
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
    public function removeTeacherFromGroup(Request $request,$id)
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

    

	
}
