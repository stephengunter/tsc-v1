<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use App\User;
use App\Volunteer;
use App\Support\Helper;

class VolunteerRequest extends FormRequest
{
  
    public function authorize()
    {
        return true;
    }

    public function rules()
    {  
        $rules=[];
        $id=$this->getVolunteerId();
        if($id){
            //Update
            return $rules;
        }
        
        // With User
        
        $user_id=$this->getUserId();       
        $userValues = $this['user'];
        $email=$userValues['email'];
        $role=Volunteer::roleName();
      
        $userRules=User::getRules($email, $user_id,$role);
        
        

        return array_merge($rules,$userRules);
    }

    public function messages()
    {
        $messages=[];

        $userMessages=User::getRuleMessages();
        return array_merge($messages,$userMessages);
    }
    public function getVolunteerId()
    {
        $id=0;
        $volunteerValues = $this['volunteer'];        
        if(array_key_exists ( 'user_id' ,$volunteerValues)){
            $id=(int)$volunteerValues['user_id'];
        }
        return $id;
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

    public function getSID()
    {
        return $this->getProfileValues()['SID'];
    } 
   
    public function getUserValues($updated_by,$removed=false)
    {
        $request=$this->get('user');
        $values=array_except($request, ['profile']);
        $values=array_except($values, ['contact_info']);
        $values= Helper::setUpdatedBy($values,$updated_by);
        return Helper::setRemoved($values,$removed);
       
    }
    public function getProfileValues($updated_by=null)
    {
        $request=$this->get('user');        
        $values=array_except($request['profile'], ['photo_id']);
        return Helper::setUpdatedBy($values,$updated_by);
        
    }
    public function getVolunteerValues($updated_by,$removed=false)
    {
        $values=$this->get('volunteer');   
        $values=array_except($values, ['user','name']);  
        $values= Helper::setUpdatedBy($values,$updated_by);
        return Helper::setRemoved($values,$removed);
        
    }
    
}
