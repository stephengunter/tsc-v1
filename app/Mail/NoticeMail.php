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
    public $attach_files;
    private $default_folder = '';
    public function __construct(Notice $notice)
    {
        $this->notice = $notice;
        $this->attach_files = $notice->getAttachments();
        
    }
    
    public function build()
    {
        
        $subject=$this->notice->title;
        $attach_files=$this->attach_files;
       
        $mail= $this->markdown('emails.notice')->with([
                            'content' => $this->notice->content
                        ])->subject($subject);
       
             
        if(count($attach_files)){
            foreach($attach_files as $attachFile){
                $path=$attachFile->storagePath();
                $mail->attach($path, [
                    'as' => $attachFile->title,
                    'mime' => $attachFile->mime,
                ]); 
            }
        }
       

        return $mail;
    }
}
