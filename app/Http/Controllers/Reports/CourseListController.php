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
       12,28,23,33,33,12,18,12,12,12
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

    public function index()
    {
        
        Excel::create('New file', function($excel) {

            $excel->sheet('New sheet', function($sheet) {
                $colWidth=$this->getColWidth();
               
                $sheet->setWidth($colWidth);
               
                $current_row=1;
                
                $sheet->row($current_row, array(
                    '慈濟大學一佰六學年度第一學期台北社會教育推廣中心'
                ));
                $sheet->setHeight($current_row, 20);
                $range=$this->getRange('A',$current_row,'J',$current_row);
                $sheet->mergeCells($range);
                $key=$this->getKey('A',$current_row);               
                $sheet->cell($key, function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $cell->setFontColor('#0000ff');
                });

                $current_row +=1;
                $sheet->row($current_row, array(
                    '課程審核清冊'
                ));
                $sheet->setHeight($current_row, 20);
                $range=$this->getRange('A',$current_row,'J',$current_row);
                $sheet->mergeCells($range);
                $key=$this->getKey('A',$current_row);               
                $sheet->cell($key, function($cell) {
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                    $cell->setFontColor('#0000ff');
                });
                

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
