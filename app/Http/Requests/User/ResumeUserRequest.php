<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use App\User;
use App\Resume;
use App\Support\Helper;

class ResumeUserRequest extends FormRequest
{
    public function authorize()
    {
        
        return true;
    }

    public function rules()
    {   
        $id=$this->getResumeId();
        $user_id=$this->getUserId(); 

        $resumeRules=[
            'resume.specialty'=> 'required',
            'resume.experiences'=> 'required',
            'resume.education'=> 'required',
        ];
        $userRules=[
            'user.profile.fullname' => 'required|max:255',
            'user.profile.SID'      => 'required|unique:profiles,SID,'.$user_id .',user_id',
            'user.phone' => 'required|max:255', 
            'user.profile.dob' => 'required',
        ];
              
        
        return array_merge($resumeRules,$userRules);
       
    }

    public function isGroup()
    {
        return $this['resume']['group'];
    }

    public function messages()
    {
       
        $messages=[

            'resume.name.required' => '必須填寫姓名',
            'resume.specialty.required' => '必須填寫專長',
            'resume.education.required' => '必須填寫最高學歷',
            'resume.experiences.required' => '必須填寫學經歷',
        ];
        
        $userMessages=User::getRuleMessages();
        return array_merge($messages,$userMessages);

        
    }
    public function getResumeId()
    {
        
        $id=0;
        $resumeValues = $this['resume'];        
        if(array_key_exists ( 'user_id' ,$resumeValues)){
            $id=(int)$resumeValues['user_id'];
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
    public function getUserValues($updated_by,$removed)
    {
        $request=$this->get('user');
        $values=array_except($request, ['profile']);
        $values=array_except($values, ['contact_info']);
        $values= Helper::setUpdatedBy($values,$updated_by);
        return Helper::setRemoved($values,$removed);
    }
    public function getProfileValues($updated_by)
    {
        $request=$this->get('user');        
        $values=array_except($request['profile'], ['photo_id']);
        return Helper::setUpdatedBy($values,$updated_by);
        
    }
    public function getResumeValues($updated_by,$removed)
    {
        $values=$this->get('resume');   
        $values=array_except($values, ['user','name']);  
        $values= Helper::setUpdatedBy($values,$updated_by);
        return Helper::setRemoved($values,$removed);
        
    }
}
