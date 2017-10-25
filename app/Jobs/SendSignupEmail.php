<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Mail;
use App\Mail\SignupEmail;
use App\User;
use App\Signup;


class SendSignupEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $signup;
    public function __construct(Signup $signup)
    {
         $this->signup = $signup;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $signup=$this->signup;
        
        $signup->user->profile;
        $signup->course->center->addressText=$signup->course->center->addressText();
        $signup->course->center->phone=$signup->course->center->phoneText(); 
        
        $user=$signup->user;
        $email=filter_var($user->email, FILTER_VALIDATE_EMAIL);
        if($email){
            Mail::to($user)->send(new SignupEmail($signup));
        }
        
    }
}
