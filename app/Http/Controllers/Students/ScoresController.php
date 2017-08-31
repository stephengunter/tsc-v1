<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Input;

use App\Repositories\Courses;

use App\Student;
use App\Score;
use App\Course;

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
                        $score->id=0;
                        $score->student_id=$student->id;
                        $score->points='';
                        $score->ps='';

                        $student->score=$score;
                    }else{
                        $score->points=round($score->points, 2);
                    }
                    
                    $student->getName();
                    $student->error='';
                   
                }
            }
          
                               
        } 

        return response()->json([ 'studentList' => $studentList]);  
    }

    public function store(Request $form)
    {
        $course=Course::findOrFail($form['course']);

        $current_user=$this->currentUser();
        if(!$course->canEditBy($current_user)){
            return  $this->unauthorized();         
        }

        $studentList=$form['studentList'];
        for($i = 0; $i < count($studentList); ++$i) {
            $student=$studentList[$i];
            $score=$student['score'];

            $score_id=$score['id'];
            $student_id=$student['id'];
            $points=$score['points'];
            $ps=$score['ps'];
            $updated_by=$current_user->id;

            $this->createOrUpdate($score_id, $student_id, $points , $ps , $updated_by);
            
        }


        return response()->json(['success' => true]);
        
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

    private function createOrUpdate($score_id, $student_id, $points , $ps , $updated_by)
    {
        $exist=Score::find($score_id);
        if($exist){
            $exist->update([
                'points' =>floatval($points),
                'ps' => $ps,
                'updated_by' => $updated_by
            ]);
        }else{
            Score::create([
                'student_id'=>$student_id,
                'points' => floatval($points),
                'ps' => $ps,
                'updated_by' => $updated_by
            ]);
        }
        
    }

   
    
    

   
   
   
}
