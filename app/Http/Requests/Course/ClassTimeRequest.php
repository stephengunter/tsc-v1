<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\Helper;


class ClassTimeRequest extends FormRequest
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
            'classtime.on' =>'required',
            'classtime.off' =>'required',
        ];
    }

    public function messages()
    {
        return [
            'classtime.on.required' => '必須選擇上課時間',
             'classtime.off.required' => '必須選擇下課時間',
            
        ];
    }
    public function getValues($updated_by,$removed)
    {
        $values=$this->get('classtime');
        $values= Helper::setUpdatedBy($values,$updated_by);
        return Helper::setRemoved($values,$removed);
    }
}
