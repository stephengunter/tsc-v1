<?php

namespace App\Mail;
use App\User;
use App\PasswordResetToken;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Support\Helper;

class EmailResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    private $user;
    private $settings;

    public function __construct(User $user, $frontend=false)
    {
        $this->user = $user;
        $this->settings = Helper::getAppSettings($frontend);
       
    }
    

    public function build()
    {
        $url = $this->getVerificationUrl();
       
        $subject=$this->settings['name'] . ' - ' .'重設密碼認證信';
       
        return $this->markdown('emails.reset password')->with([
                            'user' => $this->user,
                            'url' => $url,
                            'app_name' => $this->settings['name'],
                            'home_page' => $this->settings['url']  
                        ])->subject($subject);
    }

    

    private function createToken()
    {
        $values=PasswordResetToken::initialize();
        $user=User::find($this->user->id);
        if($user->passwordResetToken){
           $user->passwordResetToken->update($values);
        }else{
             $passwordResetToken=new PasswordResetToken($values);
             $user->passwordResetToken()->save($passwordResetToken);
        }
        
        return $values['token'];
       
    }
    private function getVerificationUrl()
    {
        $url = $this->settings['url'];
        // $spa= $this->settings['spa'];
        // $action='reset-password';
        // $query = array(
        //     'id' => $this->user->id,
        //     'token' => $this->createToken()
        // );
        $url= $url .'/reset-password/user/' . $this->user->id;
        $url= $url .'/token/' . $this->createToken();
    //    return Helper::buildUrl($spa,$url,$action,$query);

        return $url;
    }
}
