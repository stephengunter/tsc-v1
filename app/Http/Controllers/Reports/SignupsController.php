<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\BaseController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

use App\Term;
use App\Center;

use App\Support\Helper;
use App\Services\Signup\SignupService;
use App\Services\Course\CourseService;

use Carbon\Carbon;
use Excel;
use DB;

class SignupsController extends BaseController
{

    protected $key='reports';
    private $theads=array(
       [
         'key'=> 'number',
         'col'=> 'A',
         'title'=> '課程編號',
         'width' => 12
        
       ],
       [
         'key'=> 'name',
         'col'=> 'B',
         'title'=> '課程名稱',
         'width' => 24
        
       ],
       [
         'key'=> 'min',
         'col'=> 'C',
         'title'=> '最低要求',
         'width' => 12
        
       ],
       [
         'key'=> 'limit',
         'col'=> 'D',
         'title'=> '上限人數',
         'width' => 12
        
       ],
       [
         'key'=> 'success',
         'col'=> 'E',
         'title'=> '已繳費人數',
         'width' => 12
        
	   ]
	);
	
	public function __construct(SignupService $signupService,CourseService $courseService)                       
    {
        $this->signupService=$signupService;
		$this->courseService=$courseService;
    }

    private function getThead()
    {
        return collect($this->theads);
    }
    private function getCols()
    {
       return $this->getThead()->pluck('col')->toArray();
	} 
	private function getWidth()
    {
       return $this->getThead()->pluck('width')->toArray();
	} 
	private function getTheadText()
    {
       return $this->getThead()->pluck('title')->toArray();
	} 
	private function getKeys()
    {
       return $this->getThead()->pluck('key')->toArray();
    } 
   
    

    private function getColWidth()
    {
        
        return $this->getThead()->pluck('width','col')->toArray();
    }

    private function getKey($col,$row)
    {
        return $col . $row;
    }
    private function getRange($col_from,$row_from,$col_end,$row_end)
    {
        $from=$this->getKey($col_from,$row_from);
        $end=$this->getKey($col_end,$row_end);
        return $from . ':' . $end;
    }
    private function rowRange($row)
    {
        $cols=$this->getCols();
        $last=count($cols)-1;

        return $this->getRange($cols[0],$row,$cols[$last],$row);
    }

    
    private function setCourse($sheet,$current_row,$course)
    {
        $data=array(
            $course->number,
            $course->name,
			$course->min,
            $course->limit,
            $course->summary['success'],
        );

        $sheet->setHeight($current_row, 30);
        $sheet->row($current_row, $data);

        $range=$this->rowRange($current_row);
       
    }

   
    private function setTableHead($sheet,$current_row)
    { 
		
		 $sheet->row($current_row,$this->getKeys());
		 
         $sheet->setHeight($current_row, 20);
         $range=$this->rowRange($current_row);
         $sheet->cells($range, function($cells) {
            $cells->setAlignment('center');
            $cells->setValignment('center');
         });

         $current_row++;


         $sheet->row($current_row,$this->getTheadText());
         $sheet->setHeight($current_row, 20);
         $range=$this->rowRange($current_row);
         $sheet->cells($range, function($cells) {
               $cells->setAlignment('center');
               $cells->setValignment('center');
		 });

		 $current_row++;
		 return $current_row;
         
    }

   

    private function getCourseList($termId,$centerId)
    {
         $courses=$this->signupService->getReviewedCourses($termId,$centerId)
                                      ->get();
         foreach ($courses as $course) {
            $course->fullName=$course->fullName();

            $summary=$this->signupService->getSummary($course->id);
            $course->summary=$summary;

            $course->underMin=(int)$summary['success'] < $course->min;
            
         }

         return $courses;
    }

    public function index()
    {
      
         $termOptions=$this->signupService->termOptions();
         $centerOptions=$this->signupService->centerOptions();
         if(!request()->ajax()){
               $menus=$this->menus($this->key);            
               return view('reports.signups')
                     ->with([ 
                        'menus' => $menus,
                        'centerOptions' => $centerOptions,
                        'termOptions' => $termOptions
                        
                        ]);
         }  

        $request = request();
        $termId=(int)$request->term; 
        $centerId=(int)$request->center;

        $courses=$this->getCourseList($termId,$centerId);

        return response() ->json(['courses' => $courses  ]);

        
    }

    public function store(Request $request)
    {
        $termId=(int)$request['term'];
        $centerId=(int)$request['center'];

        $term=Term::findOrFail($termId);
        $center=Center::findOrFail($centerId);

        $courses=$this->getCourseList($termId,$centerId);

        if(!count($courses)) dd('查無資料');

        $title=$center->name . $term->number .'學期 報名統計表' . Carbon::today()->toDateString();
        Excel::create($title, function($excel) use ($courses){
            
            $excel->sheet('報名統計表', function($sheet) use ($courses){
               
                $colWidth=$this->getColWidth();               
                $sheet->setWidth($colWidth);

                $current_row=1;
				$current_row=$this->setTableHead($sheet,$current_row);
				
				

				foreach ($courses as $course){
					$this->setCourse($sheet,$current_row,$course);
					$current_row++;
				}

            });

        })->download('xls');
	}
	
    //匯入停止開課名單
	public function importStopCourses(Request $form)
	{
		$current_user=$this->currentUser();
        
        // if(!$current_user->isDev()){
        //     if(!$this->defaultCenter()) return  $this->unauthorized(); 
        // }
        

        if(!$form->hasFile('courses_file')){
            return   response()
                        ->json(['courses_file' => ['無法取得上傳檔案'] 
                            ]  ,  422);      
        }

       
       
        $file=Input::file('courses_file'); 

		$err_msg='';
		$excel=Excel::load($file, function($reader) {             
			$reader->limitColumns(16);
			$reader->limitRows(100);
		})->get(); 

		$courseList=$excel->toArray()[0];

		for($i = 1; $i < count($courseList); ++$i) {

			$row=$courseList[$i];


			$number=trim($row['number']);
			if(!$number)  continue;

			$course=$this->courseService->findByNumber($number);
			if(!$course) {
			   $err_msg .= '找不到代碼 = ' .$number . '的課程'. ',';
			   continue;
			}

			$stop=(int)trim($row['stop']);
 
			if(!$stop)  continue;

			
			if(!$course->canReviewBy($current_user)){
				$err_msg .= '您沒有停開此課程的權限' .$number. ' , ';
				continue;
			}

			$ps=trim($row['ps']);

			if($course->status->ps){
				$course->status->ps .= '<br>';
				$course->status->ps .= $ps;
			}else{
				$course->status->ps = $ps;
			}

			$this->courseService->stopCourse($course, $current_user,$ps);
			
		}
       

        if($err_msg)   return response()->json(['error' => $err_msg,'code' => 422 ], 422);

        return response()->json(['success' => true]);
	}


    
}
