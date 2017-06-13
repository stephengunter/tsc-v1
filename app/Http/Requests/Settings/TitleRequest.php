<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class TitleRequest extends FormRequest
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
            'title.name' => 'required|unique:titles,name,'.$id,
        ];
    }
    public function messages()
    {
        return [
            'title.name.required' => '必須填寫名稱',
            'title.name.unique' => '名稱與現存資料重複',
        ];
    }
    public function getId()
    {
        $values = $this['title'];
        $id=0;        
        if(array_key_exists ( 'id' ,$values)){
            $id=(int)$values['id'];
        }  
        return $id;
    }
}
