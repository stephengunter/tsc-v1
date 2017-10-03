<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\Helper;

class VolunteerUserRequest extends FormRequest
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
        $id=$this['user']['id'];
        return [
            'user.name' => 'required|max:255',
            'user.email'      => 'required|email|unique:users,email,'.$id,
            
            'user.phone' => 'required|max:255',  

            'user.profile.fullname' => 'required|max:255',
            
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

}
