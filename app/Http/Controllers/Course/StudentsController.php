<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

use App\Repositories\Courses;
use App\Repositories\Signups;

use App\Register;
use App\Student;
use App\Course;
use App\Admit;

use App\Support\Helper;
use App\Http\Middleware\CheckAdmin;



class StudentsController extends BaseController
{
    protected $key='students';
    public function __construct(Courses $courses,Signups $signups,CheckAdmin $checkAdmin) 
    {
       
		$exceptAdmin=[];
        $allowVisitors=[];
        $this->setMiddleware( $exceptAdmin, $allowVisitors);
        
        $this->courses=$courses;
        $this->signups=$signups;

        $this->setCheckAdmin($checkAdmin);
		
	}

   
    
    public function show($id)
    {
        $current_user=$this->currentUser();
        $student=Student::with(['user.profile'])->findOrFail($id);

        $student->canEdit=$student->canEditBy($current_user);
       

        return response()
                ->json([
                    'student' => $student
                ]);
       
    }
   
    public function edit($id)
    {
        $current_user=$this->currentUser();
        $student=Student::with(['user.profile'])->findOrFail($id);  
      
        if(!$student->canEditBy($current_user)){
            return  $this->unauthorized();  
        }  
        
        return response()->json(['student' => $student ]);        
    }
    public function update(Request $request, $id)
    {
        $current_user=$this->currentUser();
        $student=Student::findOrFail($id);
       
        if(!$student->canEditBy($current_user)){
            return  $this->unauthorized(); 
        }

        $values=$request->student;
        $student->active=$values['active'];
        $student->join_date=$values['join_date'];
        $student->ps=$values['ps'];
        $student->updated_by=$current_user->id;

        $student->save();
        
        return response() ->json([ 'student' => $student ]);   
    }

    public function destroy($id)
    {
        $student=Student::findOrFail($id);
        $current_user=$this->currentUser();
        $register=Register::findOrFail($student->course_id);
        if(!$student->canDeleteBy($current_user)){
            return  $this->unauthorized();
        }   

        $student->delete();

        if(!count($register->students)){
             $register->delete();
        }

        return response()->json(['deleted' => true ]);     
    }


   
   
   
}
