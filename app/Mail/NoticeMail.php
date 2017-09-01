<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Notice;
use Config;

class NoticeMail extends Mailable
{
    use Queueable, SerializesModels;

    private $notice;
    private $attachments;
    private  $default_folder = '';
    public function __construct(Notice $notice, Array $attachments=[])
    {
        $this->notice = $notice;
        $this->attachments = $attachments;
        $this->default_folder=Config::get('app.file_upload.default_folder');
        
    }
    
    public function build()
    {
        $subject=$this->notice->title;
        $attachments=$this->attachments;
       
        $mail= $this->markdown('emails.notice')->with([
                            'content' => $this->notice->content
                        ])->subject($subject);
                        
        if(count($attachments)){
            foreach($attachments as $attachment){
                $path='';
                $email->attach($path,['as' => '']);
            }
        }
       

        return $mail;
    }
}
