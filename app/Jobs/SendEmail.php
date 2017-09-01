<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Mail;
use App\Mail\NoticeMail;
use App\User;
use App\Notice;


class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $notice;
    private $attachments;
    private $user;
    public function __construct(Notice $notice, User $user, Array $attachments=[])
    {
         $this->notice = $notice;
         $this->user = $user;
         $this->attachments = $attachments;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $notice=$this->notice;
        $user=$this->user;
        $attachments=$this->attachments;
        
        Mail::to($user)->send(new NoticeMail($notice,$attachments));
    }
}
