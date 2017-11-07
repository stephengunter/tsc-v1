<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\Helper;

class CenterRequest extends FormRequest
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

    public function getId()
    {
        $values = $this->get('center');
       
        $id=0;        
        if(array_key_exists ( 'id' ,$values)){
            $id=(int)$values['id'];
        }  
        return $id;
    }
    public function rules()
    {
       
        $id=$this->getId();
       

        return [
            'center.name' => 'required|max:255',
            'center.code' => 'required|unique:centers,code,'. $id, 
        ];
    }
    public function messages()
    {
        return [
            'center.name.required' => '必須填寫名稱',
            'center.code.required' => '必須填寫代碼',
            'center.code.unique' => '代碼與現有中心重複',
        ];
    }

    public function getCenterValues($updated_by,$removed)
    {
        $except=['contact_info','photo_id'];

        $values=array_except($this->get('center'), $except);
        $values= Helper::setUpdatedBy($values,$updated_by);
        return Helper::setRemoved($values,$removed);
    }
}
