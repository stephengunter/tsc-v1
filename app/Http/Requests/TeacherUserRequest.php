<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\Helper;

class TeacherUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
   
    public function rules()
    {
         $user_id = 0;
         if(array_key_exists ('id' , $this['user'])){
              $user_id = (int)$this['user']['id'];
         }

         $teacherRules=[
             'teacher.specialty'=> 'required',
             'teacher.experiences'=> 'required',
             'teacher.education'=> 'required',
         ];
         $userRules=[
             'user.profile.fullname' => 'required|max:255',
             'user.profile.SID'      => 'required|unique:profiles,SID,'.$user_id .',user_id',
             'user.phone' => 'required|max:255', 
         ];
         

         

         if(!$user_id){
            $userValues = $this['user'];         
            if($userValues['email']){
               $userRules=array_add($userRules, 'user.email', 'email|unique:users,email,'.$user_id);
            }

            $newUserRules=[];


         }
      
        return [
            'user.name' => 'required|max:255',
            'user.email'      => 'required|email|unique:users,email,'.$id,
            
            'user.phone' => 'required|max:255',  

            'user.profile.fullname' => 'required|max:255',
            'user.profile.SID'      => 'required|unique:profiles,SID,'.$id .',user_id',
            
        ];
    }

    public function messages()
    {
        return [
            'user.name.required' => '必須填寫使用者名稱',

            'user.email.email' => 'Email格式不正確',
            'user.email.required' => '必須填寫Email',
            'user.email.unique' => 'Email與現存使用者重複',

             'user.phone.required' => '必須填寫手機號碼',

            'user.profile.fullname.required' => '必須填寫真實姓名',
            'user.profile.SID.required' => '必須填寫身分證號',
              'user.profile.SID.unique' => '身分證號重複',

        ];
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
