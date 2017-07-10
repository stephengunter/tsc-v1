<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Notice;

class NoticeMail extends Mailable
{
    use Queueable, SerializesModels;

    private $notice;

    public function __construct(Notice $notice)
    {
        $this->notice = $notice;
    }
    
    public function build()
    {
        $subject=$this->notice->title;
       
        return $this->markdown('emails.notice')->with([
                            'content' => $this->notice->content
                        ])->subject($subject);
    }
}
