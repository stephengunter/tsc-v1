<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\BaseController;

use Illuminate\Http\Request;

use App\Course;
use App\Repositories\Courses;
use App\Support\Helper;
use App\Http\Middleware\CheckAdmin;

use Excel;

class CourseListController extends BaseController
{
    private $cols=[
                    'A','B','C','D','E','F','G','H','I','J'
                ];
    private $width=[
       12,28,23,33,33,12,23,12,12,12
    ];
    public function __construct(Courses $courses,CheckAdmin $checkAdmin)                                          
    {
         $exceptAdmin=[];
         $allowVisitors=[];
		 $this->setMiddleware( $exceptAdmin, $allowVisitors);
		
         $this->courses=$courses;

         $this->setCheckAdmin($checkAdmin);

	}

    private function getColWidth()
    {
        $cols=$this->cols;
        $width=$this->width;
      
        $colWidth=[];
        for($i = 0; $i < count($cols); ++$i) {
            $colWidth = array_add($colWidth, $cols[$i], $width[$i]);
        }
        return $colWidth;
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
        $cols=$this->cols;
        $last=count($cols)-1;

        return $this->getRange($cols[0],$row,$cols[$last],$row);
    }

    private function setTitle($sheet,$current_row,$data)
    {
        $sheet->row($current_row, $data);
        $sheet->setHeight($current_row, 20);
        $range=$this->getRange('A',$current_row,'J',$current_row);
        $sheet->mergeCells($range);
        $key=$this->getKey('A',$current_row);               
        $sheet->cell($key, function($cell) {
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $cell->setFontColor('#0000ff');
        });
    }
    private function setCategory($sheet,$current_row,$category)
    {
        $data=array(
                   '◎' . $category->name
                );
        $sheet->row($current_row, $data);
        $sheet->setHeight($current_row, 20);
        $range=$this->getRange('A',$current_row,'J',$current_row);
        $sheet->mergeCells($range);
        $key=$this->getKey('A',$current_row);               
        $sheet->cell($key, function($cell) {
            $cell->setValignment('center');
            $cell->setFontColor('#0000ff');
        });
    }

    private function setFullBorder($sheet,$current_row)
    {
        $cols=$this->cols;
        for($i = 0; $i < count($cols); ++$i) {
            $key=$this->getKey($cols[$i],$current_row);               
            $sheet->cell($key, function($cell) {
                 $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
        }
    }
    private function setTableHead($sheet,$current_row)
    {
        $sheet->row($current_row, array(
                    '編號','課程名稱','授課教師','師資簡介','課程簡介','課程日期','上課時間',
                    '總時數','課程費用','報名日期'
                ));
        $sheet->setHeight($current_row, 20);
        $range=$this->getRange('A',$current_row,'J',$current_row);
        $sheet->cells($range, function($cells) {
             $cells->setAlignment('center');
             $cells->setValignment('center');
             $cells->setFontColor('#008000');
        });
        $this->setFullBorder($sheet,$current_row);
    }

    private function classTimes($course)
    {
        $text='';
        $classTimes=$course->getClasstimes();
        foreach ($classTimes as $classtime) {
             $text .=  $classtime->fullText() .Chr(10);    

        }
        return $text;
    }
    private function teachers($course)
    {
//         $text='';
//         $text='沙瑞金'.Chr(10);
//         $text .= ' ● ' .'翁央黨校依其' .Chr(10);
//         $text .= ' ● ' .'省委書記' .Chr(10);
//         $text .=Chr(10);
//         $text .='豪鉅工程有限公司事業已達二十餘年之久，向來堅持品質與服務為優先之經營理念，不論在施工、材料、或品質控管都有一定之專業水準，未來公司在經營之方向

// 與理念，就是把專業、用心、品質、服務 帶給每位客戶。';
        
        $teachers=$course->teachers;
        foreach ($teachers as $teacher) {
           $text .=  $teacher->getName() .Chr(10);    

        }
        return $text;
    }

    private function setCourse($sheet,$current_row,$course)
    {
        $data=array(
            $course->number,
            $course->name,
            $this->teachers($course),
            '',
            $course->description,
            $course->begin_date .  Chr(10) . '至'.  Chr(10) . $course->end_date,
            $this->classTimes($course),
            $course->hours,
            $course->tuition,
            $course->open_date  .  Chr(10) . '至'.  Chr(10) . $course->close_date ,
            
            
        );

        $sheet->setHeight($current_row, 360);
        $sheet->row($current_row, $data);

        $range=$this->rowRange($current_row);
        $sheet->getStyle($range)
                        ->getAlignment()
                        ->setWrapText(true);
        $sheet->cells($range, function($cells) {
            $cells->setAlignment('center');
            $cells->setValignment('top');
            $cells->setFontColor('#0000ff');
          
        });

        $key=$this->getKey('C',$current_row);
        $sheet->cell($key, function($cell) {
              $cell->setAlignment('left');
        });
        

        
        // $cols=$this->cols;

        //getRange($col_from,$row_from,$col_end,$row_end)
        
        // $end_row=$current_row + 24;
       
        // for($i = 0; $i < count($cols); ++$i) {
        //     $range=$this->getRange($cols[$i],$current_row,$cols[$i],$end_row);
        //     $sheet->mergeCells($range);
        // }
        //return $end_row + 1;
       
        // $sheet->getStyle('A4:B4')
        //                 ->getAlignment()
        //                 ->setWrapText(true);
    }

    public function index()
    {
        
        Excel::create('New file', function($excel) {

            $excel->sheet('New sheet', function($sheet) {
                $colWidth=$this->getColWidth();               
                $sheet->setWidth($colWidth);

                $category=\App\Category::find(1);
                $category->courses;
               
                $current_row=1;
                
                $data=array(
                    '慈濟大學一佰六學年度第一學期台北社會教育推廣中心'
                );
                $this->setTitle($sheet, $current_row, $data);
                $current_row +=1;

                $data=array(
                    '課程審核清冊'
                );
                $this->setTitle($sheet, $current_row, $data);
                $current_row +=1;

                $this->setCategory($sheet,$current_row,$category);
                $current_row +=1;

                $this->setTableHead($sheet,$current_row);
                $current_row +=1;

                $current_row = $this->setCourse($sheet,$current_row,$category->courses[0]);

                // foreach ($category->courses as $course) {
                //     $current_row = $this->setCourse($sheet,$current_row,$course);
                    
                // }

                // $sheet->row(3, array(
                //     '編號','課程名稱','授課教師','師資簡介','課程簡介','課程日期','上課時間',
                //     '總時數','課程費用','報名日期'
                // ));
                // $sheet->setHeight(3, 20);
                // $sheet->cells('A3:J3', function($cells) {
                //      $cells->setAlignment('center');
                //      $cells->setValignment('center');
                //      $cells->setFontColor('#008000');
                //      $cells->setBorder('thin', 'thin', 'thin', 'thin');
                // });
                // for ($i = 1; $i <= 10; $i++) {
                //     echo $i;
                // }
                // $sheet->cell('A1', function($cell) {
                //     // manipulate the cell
                //     $cell->setValue('data1');
                // });

                // $sheet->mergeCells('A4:A23');
                // $sheet->mergeCells('B4:B23');

                // $sheet->cells('A4:B4', function($cells) {
                //     //  $cells->setAlignment('center');
                //     $cells->setValue('data1');
                //     $cells->setValignment('top');
                //     $cells->setFontColor('#0000ff');
                //     $cells->setBorder('thin', 'thin', 'thin', 'thin');
                   
                // });

                // $sheet->getStyle('A4:B4')
                //         ->getAlignment()
                //         ->setWrapText(true);
                
                

               
              
                // $sheet->loadView('reports.courses');

            });

        })->download('xls');
    }
    
}
