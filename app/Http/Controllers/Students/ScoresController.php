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
use Excel;
use Carbon\Carbon;

class ScoresController extends BaseController
{
    protected $key='scores';
    private  $upload_folder = '/files/uploads/';
    public function __construct(Courses $courses,CheckAdmin $checkAdmin) 
    {
       
		$exceptAdmin=[];
        $allowVisitors=[];
        $this->setMiddleware( $exceptAdmin, $allowVisitors);
        
        $this->courses=$courses;

        $this->setCheckAdmin($checkAdmin);
		
    }
    private function getStudents($course_id)
    {
        $studentList=Student::where('course_id',$course_id)
        ->where('active',true)
        ->orderBy('number')
        ->get();

        return $studentList;
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
            $studentList= $this->getStudents($course_id);
            

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
    
    public function export(Request $request)
    {
        $course_id=$request['course'];        
        $course=Course::findOrFail($course_id);
        $current_user=$this->currentUser();
        if(!$course->canEditBy($current_user)){
            return  $this->unauthorized();         
        }
        
        $studentList= $this->getStudents($course_id);

       
        if(!count($studentList)) dd('查無資料');  

        foreach($studentList as $student){
            $score=$student->getScore();
            if(!$score){
                $score=new Score();
             
                $score->points='';
                $score->ps='';

                $student->score=$score;
            }else{
                $score->points=round($score->points, 2);
            }
            
            $student->getName();
           
           
        }
      
        $title=$course->number .'_student_scores';//$course->fullName() . '學員成績';
        Excel::create($title, function($excel) use ($studentList){
            
            $excel->sheet('學員成績', function($sheet) use ($studentList){
                $sheet->setFontSize(14);

                $cols=[
                    'A','B','C','D'
                ];
                $width=[
                    20,20,20,20
                 ];
                $colWidth=[];
                for($i = 0; $i < count($cols); ++$i) {
                     $colWidth = array_add($colWidth, $cols[$i], $width[$i]);
                }
                $sheet->setWidth($colWidth);
                $current_row=1;
                $sheet->row($current_row, array(
                    'number','name','score','ps'
                ));
                $sheet->setHeight($current_row, 25);
                $sheet->cells('A1:D1', function($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                    $cells->setFontColor('#0000ff');
                });
                $current_row +=1;

                foreach ($studentList as $student) {
                    $data=array(
                        $student->number,
                        $student->name,
                        $student->score->points,
                        $student->score->ps,
                    );
                    $sheet->row($current_row, $data);
                    $sheet->setHeight($current_row, 20);

                    $current_row +=1;

                }

                $current_row-=1;
                $key='A2:A' . $current_row;
                $sheet->cells($key, function($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });
                $key='B2:B' . $current_row;
                $sheet->cells($key, function($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });
                $key='C2:C' . $current_row;
                $sheet->cells($key, function($cells) {
                    $cells->setAlignment('right');
                    $cells->setValignment('center');
                });
                $key='D2:D' . $current_row;
                $sheet->cells($key, function($cells) {
                    $cells->setAlignment('right');
                    $cells->setValignment('center');
                });

              

            });

        })->download('xls');
       
    }
    private function create_file_name($file)
    {
        $timestamp = str_replace([' ', ':','-'], '', Carbon::now()->toDateTimeString());            
        return $timestamp .'_scores.' .$file->getClientOriginalExtension();         
    }
    private function save_upload_file($file) 
    {
        $folder_name= $this->upload_folder;

        $file_name = $this->create_file_name($file);  
      

        $save_path =  public_path() . $folder_name;
        
        $file->move($save_path, $file_name);
        
        return $save_path . $file_name;

       
	}
    public function import(Request $form)
    {
        $course_id=$form['course']; 
        $course=Course::findOrFail($course_id);
        $current_user=$this->currentUser();
        if(!$course->canEditBy($current_user)){
            return  $this->unauthorized();         
        }

        $updated_by=$current_user->id;

        if(!$form->hasFile('score_file')){
            return   response()
                        ->json(['score_file' => ['無法取得上傳檔案'] 
                            ]  ,  422);      
        }

        $file=Input::file('score_file');

       // $save_path = $this->save_upload_file($file);

     
        Excel::load($file, function($reader) use ($course_id,$updated_by){
             $studentList = $reader->get()->toArray();
             for($i = 0; $i < count($studentList); ++$i) {
               
                $number=(int)$studentList[$i]['number'];

                $student=Student::where('course_id',$course_id)
                                  ->where('number',$number)
                                  ->first();
                if(!$student){
                    continue;
                }  
                $points=$studentList[$i]['score'];
                $ps=$studentList[$i]['ps'];
                $student_id=$student->id;
            
                
    
                $this->createOrUpdateByStudent($student_id, $points , $ps , $updated_by);
             }  
        });

        return response()->json(['success' => true]);

       
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

    private function createOrUpdateByStudent($student_id, $points , $ps , $updated_by)
    {
        $exist=Score::where('student_id',$student_id)->first();
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
