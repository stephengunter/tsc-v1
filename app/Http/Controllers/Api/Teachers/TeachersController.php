<?php

namespace App\Http\Controllers\Api\Teachers;

use App\Http\Controllers\Api\Teachers\BaseController;
use Illuminate\Http\Request;
use App\Course;
use App\Repositories\Courses;
use App\Repositories\Teachers;

use App\Http\Requests\Teacher\TeacherUserRequest; 

use App\Support\Helper;
use DB;


class TeachersController extends BaseController
{
   
    public function __construct(Courses $courses,Teachers $teachers)                     
    {
        parent::__construct();
        $this->courses=$courses;
        $this->teachers=$teachers;
    }

    public function index()
    {
       
        $current ='/teacher';
        $menus=$this->menus($current);
        
        $current_user=$this->currentUser();
        $teacher=$current_user->teacher;
        $current_user->profile->photo=$current_user->profile->photo();
       

        return response()->json([
            'menus' => $this->menus($current),
            'user' => $current_user,
            'teacher' => $teacher
        ]);
        

    }
    
    public function store(TeacherUserRequest $request)
    {
        $current_user=$this->currentUser();
        $user=$current_user;
        $updated_by=$user->id;
        $removed=false;

        $teacherValues=$request->getTeacherValues($updated_by,$removed);
        $userValues=$request->getUserValues($updated_by,$removed);
        $profileValues=$request->getProfileValues($updated_by,$removed);

        $user= DB::transaction(function() 
        use($user,$userValues,$profileValues,$teacherValues)
        {
            
            $user->update($userValues);
            $user->profile->update($profileValues);

            if($user->teacher){
                $user->teacher->update($teacherValues);
            }else{
                $teacher=new Teacher($teacherValues);  
                $user->teacher()->save($teacher);
       
            }
            return $user;
        });
        
        
        return response()->json($user); 
    }

    
    
}
