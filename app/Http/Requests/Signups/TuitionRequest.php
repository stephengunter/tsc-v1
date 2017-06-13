<?php

namespace App\Http\Requests\Signups;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\Helper;

class TuitionRequest extends FormRequest
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
            'tuition.date' => 'required',
            
             'tuition.amount' => 'required|numeric',
          
        ];
    }

    public function messages()
    {
        return [
            'tuition.date.required' => '必須填寫繳費日期',
            
            'tuition.amount.required' => '必須填寫金額',
            'tuition.amount.numeric' => '金額必須為數字',
        ];
    }
    public function getValues($updated_by,$removed)
    {
        $values=$this->get('tuition');
        
        $values= Helper::setUpdatedBy($values,$updated_by);
        return Helper::setRemoved($values,$removed);
    }
   
}
