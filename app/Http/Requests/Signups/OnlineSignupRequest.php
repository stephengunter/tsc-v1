<?php

namespace App\Http\Requests\Signups;

use Illuminate\Foundation\Http\FormRequest;
use App\User;
use App\Support\Helper;

class OnlineSignupRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        if($this->hasUser()){
            $user=request()->user();
            $role='Student';
            return User::getRules($user->email,$user->id,$role);
            
        }else{
            return [];
        }
        
    }
    public function messages()
    {
        return User::getRuleMessages();
    }

    public function hasUser()
    {
        $userValues=$this->get('user');
        if($userValues) return true;
        return false;
    }

    
    public function getSignupValues($updated_by)
    {
        $values=$this->get('signup');
        
        return Helper::setUpdatedBy($values,$updated_by);
    }

    

    public function getUserValues($updated_by,$removed=false)
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
