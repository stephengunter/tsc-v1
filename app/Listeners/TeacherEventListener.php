<?php

namespace App\Listeners;

use App\Teacher;
use App\User;

class TeacherEventListener
{
    public function onTeacherCreated($event) 
    {
        
         $teacher=$event->teacher;
         if(!$teacher->group){
             $teacher->addToRole();
         }
         

         $current_user=$event->current_user;

         if($current_user->isAdmin()){
             $center=$current_user->admin->defaultCenter();
             if($center){
                 $teacher->attachCenter($center->id);
             }
         }
    }

    
    public function onTeacherDeleted($event) 
    {
        $teacher=$event->teacher;
        if(!$teacher->group){
            $teacher->removeRole();   
        }
           
        
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