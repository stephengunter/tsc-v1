<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\Helper;

class SignupInfoRequest extends FormRequest
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
            'signupinfo.open_date' => 'required',
            'signupinfo.close_date'=> 'required',
            
            'signupinfo.limit' => 'required|numeric',

            'signupinfo.tuition' =>'required|numeric',
            'signupinfo.cost' =>'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'signupinfo.open_date.required' => '必須填寫報名起始日',
            'signupinfo.close_date.required' => '必須填寫報名截止日',

            'signupinfo.limit.required' => '必須填寫人數上限',
            'signupinfo.limit.numeric' => '人數上限必須為數字',

            'signupinfo.tuition.required' => '必須填寫學費',
            'signupinfo.tuition.numeric' => '學費必須為數字',

            'signupinfo.cost.required' => '必須填寫材料費',
            'signupinfo.cost.numeric' => '材料費必須為數字',
        ];
    }
    public function getValues($updated_by,$removed)
    {
        $values=$this->get('signupinfo');
        $keys=[ 'close_date','limit', 'min','net_signup',
                'tuition','discount', 'cost',
                'materials'
              ];
        $values = array_only($values, $keys);
        $values= Helper::setUpdatedBy($values,$updated_by);
        return Helper::setRemoved($values,$removed);
    }
}
