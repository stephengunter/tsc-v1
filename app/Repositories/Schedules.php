<?php

namespace App\Repositories;

use App\Schedule;
use App\Support\Helper;

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
   
   
    
}