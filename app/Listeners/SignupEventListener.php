<?php

namespace App\Listeners;


class SignupEventListener
{
    
    public function updateSignupStatus($event) 
    {
        $signup=$event->signup;
        $signup->updateStatus();

    }
    public function updateRefundStatus($event) 
    {
         $refund=$event->refund;
         $refund->updateStatus();
    }

  
    public function subscribe($events)
    {
       
        $events->listen(
            'App\Events\SignupChanged',
            'App\Listeners\SignupEventListener@updateSignupStatus'
        );
        $events->listen(
            'App\Events\BackTuitionChanged',
            'App\Listeners\SignupEventListener@updateRefundStatus'
        );
        $events->listen(
            'App\Events\RefundChanged',
            'App\Listeners\SignupEventListener@updateSignupStatus'
        );
        $events->listen(
            'App\Events\TuitionChanged',
            'App\Listeners\SignupEventListener@updateSignupStatus'
        );

        

        
    }

}