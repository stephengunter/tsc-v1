<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\Helper;

class ScheduleRequest extends FormRequest
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
           
           'schedule.title' =>'required|max:255',
        ];
    }

    public function messages()
    {
        return [
          
             'schedule.title.required' => '必須填寫課目標題',
            
        ];
    }
    public function getValues($updated_by,$removed)
    {
        $values=$this->get('schedule');
        $values= Helper::setUpdatedBy($values,$updated_by);
        return Helper::setRemoved($values,$removed);
    }
}
