<?php

namespace App\Http\Requests\Signups;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\Helper;

class NewUserSignupRequest extends FormRequest
{
    
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        $rules= [

            'user.profile.fullname'  => 'required',  
            'user.profile.SID'  => 'required|min:6|unique:profiles,SID',  
            'user.profile.dob'  => 'required',  
            'user.phone' => 'required|unique:users,phone',   
            'user.email'      => 'email|unique:users,email',

            'signup.date' => 'required',
            'signup.course_id' => 'required',
            'signup.user_id'=> 'required',
            'signup.tuition'=> 'numeric',
            'signup.cost'=> 'numeric',
        ];

        if(!$this->isPay()) return $rules;
        
               
        
        $payRules=[
            'tuition.amount' => 'required|numeric|min:1',
        ];

        
        return array_merge($rules,$payRules);
    }
    public function messages()
    {
        return [
            'user.profile.fullname.required' => '必須填寫姓名',
            'user.profile.dob.required' => '必須填寫生日',
            'user.profile.SID.required' => '必須填寫身分證號',
            'user.profile.SID.min' => '身分證號格式不正確',
            'user.profile.SID.unique' => '身分證號與現存使用者重複',
            'user.phone.required' => '必須填寫手機號碼',
            'user.phone.unique' => '手機號碼與現存使用者重複',

            'user.email.email' => 'Email格式不正確',
            'user.email.unique' => 'Email與現存使用者重複',


            'signup.date.required' => '必須選擇日期',
            'signup.course_id.required' => '必須選擇報名課程',
            'signup.user_id.required' => '必須選擇姓名',

            'signup.tuition.numeric' => '必須是數字',
            'signup.cost.numeric' => '必須是數字',

            'tuition.amount.numeric' => '必須是數字',
            'tuition.amount.min' => '必須大於0',
            'tuition.amount.required' => '必須填寫金額',
        ];
    }
    public function isPay()
    {
        if($this->pay) return true;
        return false;
    }
    public function getValues($updated_by)
    {
        $values=$this->get('signup');
        return Helper::setUpdatedBy($values,$updated_by);
    }
    public function getUserValues($updated_by,$removed=false)
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
    public function getTuitionValues($updated_by)
    {
        $values=$this->get('tuition');
        return Helper::setUpdatedBy($values,$updated_by);
    }
}
