<?php

namespace App\Http\Requests\Signups;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\Helper;

class SignupRequest extends FormRequest
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
    public function rules()
    {
        return [
            'signup.date' => 'required',
            'signup.course_id' => 'required',
            'signup.user_id'=> 'required',
            'signup.tuition'=> 'numeric',
            'signup.cost'=> 'numeric',
        ];
    }
    public function messages()
    {
        return [
            'signup.date.required' => '必須選擇日期',
            'signup.course_id.required' => '必須選擇報名課程',
            'signup.user_id.required' => '必須選擇姓名',

            'signup.tuition.numeric' => '必須是數字',
            'signup.cost.numeric' => '必須是數字',
        ];
    }
    public function getValues($updated_by)
    {
        $values=$this->get('signup');
        return Helper::setUpdatedBy($values,$updated_by);
    }
}
