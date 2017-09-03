<?php

namespace App\Listeners;

use App\Events\NoticeMailCreated;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Support\Helper;
use App\Jobs\SendEmail;

class SendEmailNotice  //implements ShouldQueue
{
    
    public function __construct()
    {
        //
    }

    public function handle(NoticeMailCreated $event)
    {
        $notice=$event->notice;
        $courses=$notice->courses;

        $receivers='';    
         
       
        foreach ($courses as $course) {
            $students=$course->students;
            foreach ($students as $student) {
                $user=$student->user;
                $receivers .= $user->id . ',';
                dispatch(new \App\Jobs\SendEmail($notice, $user));  
            }

        }

        \App\Email::create([
            'notice_id' => $notice->id,
            'title'=> $notice->title,
            'content'=> $notice->content,
            'updated_by' =>  $notice->updated_by,
            'receivers' => Helper::removeLastComma($receivers),
            'attachments' => $notice->attachments
        ]);   

      
    }
}
