<?php

namespace App\Http\Requests\Signups;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\Helper;

class SignupRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        $rules=[
            'signup.date' => 'required',
            'signup.course_id' => 'required',
            'signup.user_id'=> 'required',
            'signup.tuition'=> 'numeric',
            'signup.cost'=> 'numeric',
        ];

        if(!$this->isPay()) return $rules;

       

        $payRules=[
            'tuition.amount' => 'required|numeric|min:1',
        ];

        
        return array_merge($rules,$payRules);
    }
    public function messages()
    {
        return [
            'signup.date.required' => '必須選擇日期',
            'signup.course_id.required' => '必須選擇報名課程',
            'signup.user_id.required' => '必須選擇姓名',

            'signup.tuition.numeric' => '必須是數字',
            'signup.cost.numeric' => '必須是數字',

            'tuition.amount.numeric' => '必須是數字',
            'tuition.amount.min' => '必須大於0',
            'tuition.amount.required' => '必須填寫金額',
        ];
    }

    public function isPay()
    {
        if($this->pay) return true;
        return false;
    }
    public function getValues($updated_by)
    {
        $values=$this->get('signup');
        
        return Helper::setUpdatedBy($values,$updated_by);
    }

    public function getTuitionValues($updated_by)
    {
        $values=$this->get('tuition');
        return Helper::setUpdatedBy($values,$updated_by);
    }
}
