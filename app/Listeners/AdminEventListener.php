<?php

namespace App\Listeners;

use App\Admin;
use App\User;

class AdminEventListener
{
    public function onAdminCreated($event) 
    {
        
         $admin=$event->admin;
         $admin->addToRole();

         
    }

    
    public function onAdminDeleted($event) 
    {
        $admin=$event->admin;
        $admin->removeRole();      
        
    }
    

  
    public function subscribe($events)
    {
        $events->listen(
            'App\Events\AdminCreated',
            'App\Listeners\AdminEventListener@onAdminCreated'
        );
       
        $events->listen(
            'App\Events\AdminDeleted',
            'App\Listeners\AdminEventListener@onAdminDeleted'
        );
        
    }

}