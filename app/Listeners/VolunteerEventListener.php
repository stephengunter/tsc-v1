<?php

namespace App\Listeners;

use App\Volunteer;
use App\User;

class VolunteerEventListener
{
    public function onVolunteerCreated($event) 
    {
        
         $volunteer=$event->volunteer;
         $volunteer->addToRole();

         $current_user=$event->current_user;

         if($current_user->isAdmin()){
             $center=$current_user->admin->validCenters()->first();
             if($center){
                 $volunteer->attachCenter($center->id);
             }
         }
    }

    
    public function onVolunteerDeleted($event) 
    {
        $volunteer=$event->volunteer;
        $volunteer->removeRole();      
        
    }
    

  
    public function subscribe($events)
    {
        $events->listen(
            'App\Events\VolunteerCreated',
            'App\Listeners\VolunteerEventListener@onVolunteerCreated'
        );
       
        $events->listen(
            'App\Events\VolunteerDeleted',
            'App\Listeners\VolunteerEventListener@onVolunteerDeleted'
        );
        
    }

}