<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

use App\Repositories\Courses;
use App\Repositories\Signups;

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
        $student->ps=$values['ps'];
        $student->updated_by=$current_user->id;

        $student->save();
        
        return response() ->json([ 'student' => $student ]);   
    }

    public function destroy($id)
    {
        $admit=Admit::findOrFail($id); 
        $student=Student::findOrFail($admit->course_id);
        $current_user=$this->currentUser();

        if(!$admit->canDeleteBy($current_user)){
            return  $this->unauthorized();
        }    

        $admit->delete();

        if(!count($student->admits)){
             $student->delete();
        }

        return response()->json(['deleted' => true ]);     
    }


    private function getAdmitSummary($course)
    {
        $signup_ids=Admit::where('course_id',$course->id)->get()->pluck('signup_id');
        $info = DB::table('signups')
                        ->whereIn('id',$signup_ids)
                        ->select('status', DB::raw('count(*) as total'))
                        ->groupBy('status')->get();
        return $this->signups->printSignupSummary($info); 
    }
    
   
   
}
