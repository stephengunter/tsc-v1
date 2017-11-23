<?php

namespace App\Http\Requests\Discounts;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\Helper;

class DiscountRequest extends FormRequest
{
    /**
     * Dediscountine if the user is authorized to make this request.
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
            'discount.name' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'discount.name.required' => '必須填寫名稱',
        ];
    }
    public function getId()
    {
        $values = $this['discount'];
        $id=0;        
        if(array_key_exists ( 'id' ,$values)){
            $id=(int)$values['id'];
        }  
        return $id;
    }
    public function getValues($updated_by,$removed)
    {
        $values = $this['discount'];
        $values= Helper::setUpdatedBy($values,$updated_by);
        return Helper::setRemoved($values,$removed);
        
    }
}
