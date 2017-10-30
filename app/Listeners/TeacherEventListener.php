<?php

namespace App\Listeners;

use App\Teacher;
use App\User;

class TeacherEventListener
{
    public function onTeacherCreated($event) 
    {
        
        $teacher=$event->teacher;
        if($teacher->group) return;

        $teacher->addToRole();
         

        
    }

    
    public function onTeacherDeleted($event) 
    {
        $teacher=$event->teacher;
        if($teacher->group) return;
        
        $teacher->removeRole();
           
        
    }
    

  
    public function subscribe($events)
    {
        $events->listen(
            'App\Events\TeacherCreated',
            'App\Listeners\TeacherEventListener@onTeacherCreated'
        );
        
        $events->listen(
            'App\Events\TeacherDeleted',
            'App\Listeners\TeacherEventListener@onTeacherDeleted'
        );
        
    }

}