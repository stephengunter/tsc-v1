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
        $courses=$notice->courses;
        foreach ($courses as $course) {
            $students=$course->students;
            foreach ($students as $student) {
                 $user=$student->user;
                 dispatch(new SendEmail($notice, $user));   
            }

        }
    }
}
