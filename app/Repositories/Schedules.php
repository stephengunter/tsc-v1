<?php

namespace App\Repositories;

use App\Schedule;
use App\Support\Helper;

use Excel;

class Schedules 
{
    public function initialize($course_id)
    {
        $order=Schedule::where('course_id',$course_id)->max('order');
		if(!$order) $order=1;
		else $order+=1;
        return [
			 'course_id' => $course_id,
			 'order' => $order,
        ];
    }
   
    public function findOrFail($id)
    {
        $schedule = Schedule::findOrFail($id);
        return $schedule;
       
    }
    public function getByCourse($course)
    {
        return Schedule::where('course_id', $course)->orderBy('order');
           
    }
  
    public function import($fromCourse,$toCourse,$updated_by)
    {
         $fromSchedules=$fromCourse->schedules;
         foreach($fromSchedules as $oldSchedule){
             $values=array_except($oldSchedule->toArray(),['id']);
             $values['course_id']= $toCourse->id;
             $values=Helper::setUpdatedBy($values,$updated_by);

             Schedule::create($values);
             
         }
    }

   

    public function optionsByCenter($center)
    {
        $center=Center::findOrFail($center);
        $users=$center->teachers();

        $options=[];
        foreach($users as $user)
        {
            $item=[ 'text' => $user->teacher->name , 
                    'value' => $user->id , 
                ];
            array_push($options,  $item);
        }

        return $options;
       
    }

    public function options($course)
    {
        $course=Course::findOrFail($course);
        return $this->optionsConverting($course->teachers);
       
    }


    public function optionsConverting($teacherList)
    {
        $options=[];
        foreach($teacherList as $teacher)
        {
            $item=[ 'text' => $teacher->name , 
                     'value' => $teacher->user_id , 
                 ];
            array_push($options,  $item);
        }

        return $options;
    }

    public function importSchedules($file,$course_id,$current_user)
    {
        $err_msg='';
        
        $excel=Excel::load($file, function($reader) {             
            $reader->limitColumns(16);
            $reader->limitRows(100);
        })->get();
        

        $scheduleList=$excel->toArray()[0];
        for($i = 1; $i < count($scheduleList); ++$i) {
            $row=$scheduleList[$i];
            
            $title=trim($row['title']);
            if(!$title)continue;

            $order=(int)trim($row['order']);

            $content=trim($row['content']);
            $materials=trim($row['materials']);
            

            $values=[
                'course_id' => $course_id,
                'order' => $order ,
                'title' => $title , 
                'content' => $content,
                'materials' => $materials,

                'updated_by' => $current_user->id
            ];

            
            $schedule = Schedule::create($values);
            
        }

        return $err_msg;
    }
   
   
    
}