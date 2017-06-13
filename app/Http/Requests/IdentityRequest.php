<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IdentityRequest extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
       
        return [
            'identity.name' => 'required|unique:identities,name,'.$this['identity']['id'],
        ];
    }
    public function messages()
    {
        return [
            'identity.name.required' => '必須填寫名稱',
            'identity.name.unique' => '名稱與現存資料重複',
        ];
    }
    public function getValues()
    {
         return  $this->get('identity');
       
    }
}
