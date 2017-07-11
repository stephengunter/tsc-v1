<?php

namespace App\Mail;

use App\Signup;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Support\Helper;

class AdmitEmail extends Mailable
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
        $subject=$this->settings['name'] . ' - ' .'錄取與繳費通知信';
       
        return $this->markdown('emails.admission')->with([
                            'signup' => $this->signup,
                            'settings' => $this->settings,
                        ])->subject($subject);

    }

    

    
    
}
