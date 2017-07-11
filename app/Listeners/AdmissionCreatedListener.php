<?php

namespace App\Listeners;

use App\Events\AdmissionCreated;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Jobs\SendAdmitEmail;

class AdmissionCreatedListener  implements ShouldQueue
{
    
    public function __construct()
    {
        //
    }

    public function handle(AdmissionCreated $event)
    {
        $admission=$event->admission;
       
        $admits=$admission->admits;
        foreach ($admits as $admit) {
            $signup=$admit->signup;
            dispatch(new SendAdmitEmail($signup));   

        }
        
    }
}
