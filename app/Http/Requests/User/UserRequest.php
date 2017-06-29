<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use App\User;
use App\Support\Helper;

class UserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
   
    public function rules()
    {
        $userValues = $this['user'];
        $email=$userValues['email'];   

        $user_id=$this->getUserId();
        $role=$this->getRole();
        if($user_id){
           $user = User::findOrFail($user_id);
           if($user->roles()->first()){
              $role=$user->roles()->first()->name;
           }

        }
        
        
        return User::getRules($email,$user_id,$role);

        
    }

    public function messages()
    {
        return User::getRuleMessages();
    }
   
    public function getUserId()
    {
        $userValues = $this['user'];
        $user_id=0;        
        if(array_key_exists ( 'id' ,$userValues)){
            $user_id=(int)$userValues['id'];
        }  
        return $user_id;
    }
    public function getRole()
    {
        $userValues = $this['user'];
        $role='User';        
        if(array_key_exists ( 'role' ,$userValues)){
            $role=$userValues['role'];
        }  
        return $role;
    }

    public function getUserValues($updated_by,$removed)
    {
        $request=$this->get('user');
        $values=array_except($request, ['profile','contact_info','role']);
       
        $values= Helper::setUpdatedBy($values,$updated_by);
        return Helper::setRemoved($values,$removed);
    }
    public function getProfileValues($updated_by)
    {
        $request=$this->get('user');        
        $values=array_except($request['profile'], ['photo_id']);
        return Helper::setUpdatedBy($values,$updated_by);
        
    }

}
