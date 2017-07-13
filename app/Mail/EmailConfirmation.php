<?php

namespace App\Mail;

use App\User;
use App\EmailToken;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Support\Helper;

class EmailConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    private $user;
    private $settings;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->settings = config('app.frontend');
       
       
    }
    

    public function build()
    {
        $url = $this->getVerificationUrl();
        $subject=$this->settings['name'] . ' - ' .'會員註冊認證信';
       
        return $this->markdown('emails.confirmation')->with([
                            'user' => $this->user,
                            'url' => $url,
                            'app_name' => $this->settings['name'],
                            'home_page' => $this->settings['url']  
                        ])->subject($subject);
    }

    

    private function createToken()
    {
        $values=EmailToken::initialize();
        $user=User::find($this->user->id);
        if($user->emailToken){
           $user->emailToken->update($values);
        }else{
             $emailToken=new EmailToken($values);
             $user->emailToken()->save($emailToken);
        }
        
        return $values['token'];
       
    }
    private function getVerificationUrl()
    {
        
        $url = $this->settings['url'];
        $spa= $this->settings['spa'];
     
        $action='email-confirmation';

        if($spa){
            $query = array(
                'user' => $this->user->id,
                'token' => $this->createToken()
            );
            return Helper::buildUrl($spa,$url,$action,$query);
        }
        

        $url= $url .'/' . $action . '/user/' . $this->user->id;
        $url= $url .'/token/' . $this->createToken();
         return $url;

        
    }
}
