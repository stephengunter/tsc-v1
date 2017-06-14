<?php

namespace App\Http\Requests\Discounts;

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
        $id=$this->getId();
        return [
            'identity.name' => 'required|unique:identities,name,'.$id,
        ];
    }
    public function messages()
    {
        return [
            'identity.name.required' => '必須填寫名稱',
            'identity.name.unique' => '名稱與現存資料重複',
        ];
    }
    public function getId()
    {
        $values = $this['identity'];
        $id=0;        
        if(array_key_exists ( 'id' ,$values)){
            $id=(int)$values['id'];
        }  
        return $id;
    }
    public function getValues()
    {
         return  $this->get('identity');
       
    }
}
