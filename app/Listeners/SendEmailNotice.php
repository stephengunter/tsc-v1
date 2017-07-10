<?php

namespace App\Listeners;

use App\Events\NoticeMailCreated;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Jobs\SendEmail;

class SendEmailNotice  implements ShouldQueue
{
    
    public function __construct()
    {
        //
    }

    public function handle(NoticeMailCreated $event)
    {
        $notice=$event->notice;
        // $courses=
        // if($user->email){
        //     dispatch(new SendEmail($notice));
        // }
         
       
    }
}
