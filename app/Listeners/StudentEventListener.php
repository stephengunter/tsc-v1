<?php

namespace App\Listeners;

use App\Student;

class StudentEventListener
{
    public function updateStudent($event) 
    {
        $signup=$event->signup;
        $student=Student::where('user_id',$signup->user_id)
                          ->where('course_id',$signup->course_id)
                          ->get();
        if($student){
            $student->active=false;
            $student->out_date=$signup->refund->date;
            $student->save();
        }


    }

  
    public function subscribe($events)
    {
        
        $events->listen(
            'App\Events\RefundChanged',
            'App\Listeners\StudentEventListener@updateStudent'
        );
        
    }

}