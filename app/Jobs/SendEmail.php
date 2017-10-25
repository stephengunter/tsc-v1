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
    private $user;
    public function __construct(Notice $notice, User $user)
    {
         $this->notice = $notice;
         $this->user = $user;
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

        $email=filter_var($user->email, FILTER_VALIDATE_EMAIL);
        if($email){
            Mail::to($user)->send(new NoticeMail($notice));
        }
        
        
    }
}
