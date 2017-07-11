<?php

namespace App\Mail;

use App\Signup;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Support\Helper;

class SignupEmail extends Mailable
{
    use Queueable, SerializesModels;

    private $signup;
    private $settings;
    
    public function __construct(Signup $signup)
    {
       $this->signup = $signup;
       $this->settings = config('app.frontend');
    }
    

    public function build()
    {
        $subject=$this->settings['name'] . ' - ' .'報名成功通知信';
       
        return $this->markdown('emails.signup')->with([
                            'signup' => $this->signup,
                            'settings' => $this->settings,
                        ])->subject($subject);

    }

    

    
    
}
