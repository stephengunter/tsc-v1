<?php

namespace App\Listeners;

use App\Course;
use App\User;

class CourseEventListener
{
    
    public function onCourseUpdateded($event) 
    {
        $course=$event->course;
        $course->status->updateStatus();
        
    }
    

  
    public function subscribe($events)
    {
        $events->listen(
            'App\Events\CourseUpdated',
            'App\Listeners\CourseEventListener@onCourseUpdateded'
        );
        
        
        
    }

}