<?php

namespace App\Listeners;

use App\ContactInfo;
use App\User;
use App\Center;

class ContactInfoEventListener
{
    public function onContactInfoCreated($event) 
    {
        
        $contactInfo=$event->contactInfo;
        $current_user=$event->current_user;
        $user_id=$event->user_id;
        $center_id=$event->center_id;
       
        if($user_id){
            $user=User::find($user_id);
            if($user && $user->canEditBy($current_user)){
               
                $user->contact_info=$contactInfo->id;
                $user->updated_by=$current_user->id;
                $user->save();
            }
        }
        if($center_id){
            $center=Center::find($center_id);
            if($center && $center->canEditBy($current_user)){
                $center->contact_info=$contactInfo->id;
                $center->updated_by=$current_user->id;
                $center->save();
            }
        }   
    }

    
    
    

  
    public function subscribe($events)
    {
        $events->listen(
            'App\Events\ContactInfoCreated',
            'App\Listeners\ContactInfoEventListener@onContactInfoCreated'
        );
       
        
    }

}