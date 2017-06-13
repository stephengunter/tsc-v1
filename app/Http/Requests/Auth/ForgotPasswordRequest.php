<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
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
             'email' => 'required|email'
        ];
    }
    public function messages()
    {
        return [
            'email.email' => 'Email格式不正確',
            'email.required' => '必須填寫Email',
        ];
    }
}
