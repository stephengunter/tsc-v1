<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Mail;
use App\Mail\AdmitEmail;
use App\User;
use App\Signup;

use App\Support\Helper;

class SendAdmitEmail implements ShouldQueue
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

        $signup->tuition =Helper::formatMoney($signup->tuition);
        $signup->points =$signup->formattedPoints();
        $signup->user->profile;
        $signup->course->center->addressText=$signup->course->center->addressText();
        $signup->course->center->phone=$signup->course->center->phoneText(); 

        
       
        Mail::to($signup->user)->send(new AdmitEmail($signup));
    }
}
