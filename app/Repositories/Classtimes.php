<?php

namespace App\Repositories;

use App\ClassTime;
use App\Course;
use App\Weekday;

use Excel;

class Classtimes 
{
     
    public function getByCourse($course_id)
    {
        return ClassTime::with('weekday')->where('course_id',$course_id);
    }
    
    public function importAll($file,$current_user)
    {
        $err_msg='';
        
        $excel=Excel::load($file, function($reader) {             
            $reader->limitColumns(16);
            $reader->limitRows(500);
        })->get();
        

        $classtimeList=$excel->toArray()[0];
        for($i = 1; $i < count($classtimeList); ++$i) {
            $row=$classtimeList[$i];

            $number=trim($row['number']);
            if(!$number){
                continue;
            }

            $course=Course::where('removed',false)->where('number',$number)->first();
            if(!$course){
                $err_msg .= '代碼  ' .$number . ' 錯誤'. ',';
                continue;
            }
            
            $weekday=trim($row['weekday']);
            if(!$weekday)continue;

            $on=trim($row['on']);
            if(!$on)continue;

            $off=trim($row['off']);
            if(!$off)continue;

            $weekday=(int)$weekday;
            $weekdayEntity=Weekday::where('val',$weekday)->first();
            if(!$weekdayEntity){
                $err_msg .= '禮拜幾  ' .$number . ' 錯誤'. ',';
                continue;
            }



            $on=(int)$on;
            $off=(int)$off;

            
            

            $values=[
                'course_id' => $course->id,
                'weekday_id' => $weekdayEntity->id ,
                'on' => $on , 
                'off' => $off,

                'updated_by' => $current_user->id
            ];

            
            $classtime = ClassTime::create($values);
            
        }

        return $err_msg;
    }
    
   

  
  
   
   
    
}