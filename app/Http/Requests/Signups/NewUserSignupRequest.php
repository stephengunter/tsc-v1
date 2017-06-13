<?php

namespace App\Http\Requests\Signups;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\Helper;

class NewUserSignupRequest extends FormRequest
{
    /**
     * Detitleine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return [

            'user.profile.fullname'  => 'required',  
            'user.profile.SID'  => 'required|min:6|unique:profiles,SID',  
            'user.profile.dob'  => 'required',  
            'user.phone' => 'required|unique:users,phone',   
            'user.email'      => 'email|unique:users,email',

            'signup.date' => 'required',
            'signup.course_id' => 'required',
            'signup.user_id'=> 'required',
        ];
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
        ];
    }
    public function getValues($updated_by)
    {
        $values=$this->get('signup');
        return Helper::setUpdatedBy($values,$updated_by);
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
}
