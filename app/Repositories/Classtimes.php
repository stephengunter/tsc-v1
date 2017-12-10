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
        
        $classtimes=[];

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
            if($course->reviewed){
                $err_msg .= '代碼  ' .$number . ' 已審核,無法更改'. ',';
                continue;
            }

            if(!$course->canEditBy($current_user)){
                $err_msg .= '您沒有編輯課程  ' .$number . ' 的權限'. ',';
                continue;        
            }

          
            
            $weekday=(int)trim($row['weekday']);

            $on=(int)trim($row['on']);

            $off=(int)trim($row['off']);
            $classroom=trim($row['classroom']);
           

            $weekday=(int)$weekday;
            $weekdayEntity=Weekday::where('val',$weekday)->first();
            if(!$weekdayEntity){
                $err_msg .= '星期代碼  ' .$number . ' 錯誤'. ',';
                continue;
            }
            

            $on=(int)$on;
            $off=(int)$off;
            
            

            $values=[
                'course_id' => $course->id,
                'weekday_id' => $weekdayEntity->id ,
                'on' => $on , 
                'off' => $off,
                'classroom' => $classroom,
                'updated_by' => $current_user->id
            ];

            $classtime =new ClassTime($values);

            array_push($classtimes, $classtime);
            
        }

        $course_ids = array_column($classtimes, 'course_id');
        $courses=Course::whereIn('id',$course_ids)->get();
       
        foreach($courses as $course){
            $course->classTimes()->delete();
        }

        foreach($classtimes as $classtime){
            $classtime->save();
        }




        return $err_msg;
    }
    
   

  
  
   
   
    
}