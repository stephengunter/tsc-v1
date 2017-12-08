<?php

namespace App\Http\Requests\Signups;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\Helper;

class PayRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
       

        return [
            'tuition.amount' => 'required|numeric|min:1',
        ];

        
        
    }
    public function messages()
    {
        return [
           
            'tuition.amount.min' => '必須大於0',
            'tuition.amount.required' => '必須填寫金額',
        ];
    }

   
    public function getBillValues($updated_by)
    {
        $values=$this->get('bill');
        
        return Helper::setUpdatedBy($values,$updated_by);
    }

    public function getTuitionValues($updated_by)
    {
        $values=$this->get('tuition');
        return Helper::setUpdatedBy($values,$updated_by);
    }
    public function getSignups()
    {
        return array_map(function($item){
            return new \App\Signup($item);
        }, $this->get('signups'));
    }
}
