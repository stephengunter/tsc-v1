<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\Helper;

class RegisterRequest extends FormRequest
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
       
        return [
            'user.name' => 'required|max:255',
            'user.phone' => 'max:255',   
            'user.email'      => 'required|email|unique:users,email',
            'user.password' => 'min:6|required',
            'user.confirmation' => 'required|same:user.password'

        ];
    }

    public function messages()
    {
        return [
            'user.name.required' => '必須填寫使用者名稱',

            'user.email.email' => 'Email格式不正確',
            'user.email.required' => '必須填寫Email',
            'user.email.unique' => 'Email與現存使用者重複',

            'user.password.required' => '必須填寫密碼',
            'user.password.min' => '密碼長度不足(最少6位)',

            'user.confirmation.required' => '必須填寫確認密碼',
            'user.confirmation.same' => '確認密碼錯誤',

        ];
    }

    public function getValues()
    {
        $request=$this->get('user');
        $values=array_except($request, ['confirmation']);
        return Helper::setRemoved($values,false);
    }
    

    

}
