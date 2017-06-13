<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\Helper;

class FindUserRequest extends FormRequest
{
    
    public function authorize()
    {
        return true;
    }

   
    public function rules()
    {
        $values = $this['user'];
        if($values['email']){
           return [
             'user.email'      => 'email',
             'user.phone' => 'required|min:6',  
           ];
       }else{
           return [
              'user.phone' => 'required|min:6',  
           ];
       }
   
    }

    public function messages()
    {
        return [
            'user.phone.required' => '必須填寫手機號碼',
            'user.phone.min' => '手機號碼格式不正確',
            'user.email.email' => 'Email格式不正確',
         

        ];
    }
    public function getValues()
    {
        return $this->get('user');
    }

}
