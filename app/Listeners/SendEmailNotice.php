<?php

namespace App\Listeners;

use App\Events\NoticeMailCreated;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Jobs\SendEmail;
use App\Email;

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
        // $receivcers='';
        // $fillable = [ 
        //     'notice_id','title' , 'content',      
        //     'from', 'receivers' , 'attachments','updated_by'                           
        //   ];
        // $email=new Email([
        //     'notice_id' => $notice->id,
        //     'title'=> $notice->title,
        //     'content'=> $notice->content,
        //     'updated_by' =>  $notice->updated_by,
        // ]);
        foreach ($courses as $course) {
            $students=$course->students;
            foreach ($students as $student) {
                 $user=$student->user;
                //  $receivcers .= $user->id;
                 dispatch(new SendEmail($notice, $user));   
                //  if($students->hasNext()){
                //     $receivcers .= ',';
                //  }
            }

        }

        // $email->receivers=$receivcers;
        // $email->save();
    }
}
