<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;
use App\User;
use App\Teacher;
use App\Support\Helper;

class TeacherRequest extends FormRequest
{
    public function authorize()
    {
        
        return true;
    }

    public function rules()
    {   
       
        if($this->isGroup()){
            return [
                'teacher.name'=> 'required',
               
            ]; 
        }
       
        $rules=[
             'teacher.specialty'=> 'required',
             'teacher.experiences'=> 'required',
             'teacher.education'=> 'required',
        ];
        
        return $rules;

        $id=$this->getTeacherId();
        if($id){
            //Update
            return $rules;
        }
        
        // With User
        
        $user_id=$this->getUserId();       
        $userValues = $this['user'];
        $email=$userValues['email'];
        $role=Teacher::roleName();
      
        $userRules=User::getRules($email, $user_id,$role);

        return array_merge($rules,$userRules);
       
    }

    public function isGroup()
    {
        return $this['teacher']['group'];
    }

    public function messages()
    {
        $messages=[

            'teacher.name.required' => '必須填寫教師姓名',
            'teacher.specialty.required' => '必須填寫專長',
            'teacher.education.required' => '必須填寫最高學歷',
            'teacher.experiences.required' => '必須填寫學經歷',
        ];
        
        $userMessages=User::getRuleMessages();
        return array_merge($messages,$userMessages);

        
    }
    public function getTeacherId()
    {
        
        $id=0;
        $teacherValues = $this['teacher'];        
        if(array_key_exists ( 'user_id' ,$teacherValues)){
            $id=(int)$teacherValues['user_id'];
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
    public function getTeacherValues($updated_by,$removed)
    {
        $values=$this->get('teacher');   
        $values=array_except($values, ['user','name']);  
        $values= Helper::setUpdatedBy($values,$updated_by);
        return Helper::setRemoved($values,$removed);
        
    }
}
