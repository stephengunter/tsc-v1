<?php

namespace App\Listeners;

use App\Events\SignupCreated;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Jobs\SendSignupEmail;

class SignupCreatedListener  implements ShouldQueue
{
    
    public function __construct()
    {
        //
    }

    public function handle(SignupCreated $event)
    {
        $signup=$event->signup;
        if($signup->net_signup){
            dispatch(new SendSignupEmail($signup));  
        }
         
    }
}
