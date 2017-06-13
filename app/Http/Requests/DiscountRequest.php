<?php

namespace App\Http\Requests;

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
        $id=$this['discount']['id'];
        return [
            'discount.name' => 'required|max:255',
        ];
    }
    public function messages()
    {
        return [
            'discount.name.required' => '必須填寫名稱',
        ];
    }
    public function getValues($updated_by,$removed)
    {
        $values=array_except($this->get('discount'), ['canDelete']); 
        $values= Helper::setUpdatedBy($values,$updated_by);
        return Helper::setRemoved($values,$removed);
        
    }
}
