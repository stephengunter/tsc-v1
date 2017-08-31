<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Input;

use App\Repositories\Courses;

use App\Student;
use App\Score;

use App\Support\Helper;
use App\Http\Middleware\CheckAdmin;



class ScoresController extends BaseController
{
    protected $key='scores';
    public function __construct(Courses $courses,CheckAdmin $checkAdmin) 
    {
       
		$exceptAdmin=[];
        $allowVisitors=[];
        $this->setMiddleware( $exceptAdmin, $allowVisitors);
        
        $this->courses=$courses;

        $this->setCheckAdmin($checkAdmin);
		
    }
    public function index()
    {
      
        $request = request();
        
        if(!$request->ajax()){
            $menus=$this->menus($this->key);            
            return view('scores.index')
                  ->with(['menus' => $menus]);
        }  

        $studentList=[];
        $course_id=(int)$request->course;
        
        if($course_id > 0){
            $studentList=Student::where('course_id',$course_id)
            ->where('active',true)
            ->orderBy('number')
            ->get();

            if(count($studentList)){
                foreach($studentList as $student){
                    $score=$student->getScore();
                    if(!$score){
                        $score=new Score();
                        $score->student_id=$student->id;
                        $score->points='';

                        $student->score=$score;
                    }
                    
                    $student->getName();
                    $student->error='';
                   
                }
            }
          
                               
        } 

        return response()->json([ 'studentList' => $studentList]);  
    }

    public function import(Request $form)
    {
        if(!$form->hasFile('score_file')){
            return   response()
                        ->json(['score_file' => ['無法取得上傳檔案'] 
                            ]  ,  422);      
        }

        $file=Input::file('score_file');

        dd($file);
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
