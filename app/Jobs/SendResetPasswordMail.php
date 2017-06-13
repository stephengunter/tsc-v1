<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Mail;
use App\User;
use App\Mail\EmailResetPassword;
use App\Support\Helper;

class SendResetPasswordMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user;
    private $frontend;

    public function __construct(User $user, $frontend=false)
    {
       $this->user = $user;
       $this->frontend = $frontend;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user=$this->user;
        $frontend=$this->frontend;
        Mail::to($user)->send(new EmailResetPassword($user, $frontend));
    }
}
