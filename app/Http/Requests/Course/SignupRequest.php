<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\Helper;

class SignupRequest extends FormRequest
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
            'signup.open_date' => 'required',
            'signup.close_date'=> 'required',
            
             'signup.limit' => 'required|numeric',

            'signup.tuition' =>'required|numeric',
            'signup.cost' =>'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'signup.open_date.required' => '必須填寫報名起始日',
            'signup.close_date.required' => '必須填寫報名截止日',

            'signup.limit.required' => '必須填寫人數上限',
            'signup.limit.numeric' => '人數上限必須為數字',

             'signup.tuition.required' => '必須填寫學費',
            'signup.tuition.numeric' => '學費必須為數字',

             'signup.cost.required' => '必須填寫材料費',
            'signup.cost.numeric' => '材料費必須為數字',
        ];
    }
    public function getValues($updated_by,$removed)
    {
        $values=$this->get('signup');
        $keys=[ 'open_date','close_date','limit','tuition',
            'cost','materials','net_signup'
        ];
        $values = array_only($values, $keys);
        $values= Helper::setUpdatedBy($values,$updated_by);
        return Helper::setRemoved($values,$removed);
    }
}
