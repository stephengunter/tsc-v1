<?php

namespace App\Listeners;

use App\Events\SignupCreated;


use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Jobs\SendSignupEmail;


class SignupEventListener
{
    public function onSignupCreated(SignupCreated $event)
    {
        $signup=$event->signup;
       
        
        if($signup->net_signup){
            dispatch(new SendSignupEmail($signup));  
        }
    }
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
            'App\Events\SignupCreated',
            'App\Listeners\SignupEventListener@updateSignupStatus'
        );
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