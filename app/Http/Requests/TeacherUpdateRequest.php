<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use App\Support\Helper;

class TeacherUpdateRequest extends FormRequest
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
             'teacher.specialty'=> 'required',
             'teacher.experiences'=> 'required',
             'teacher.education'=> 'required',
        ];
    }

    public function messages()
    {
        return [
           
            'teacher.specialty.required' => '必須填寫專長',
            'teacher.education.required' => '必須填寫最高學歷',
            'teacher.experiences.required' => '必須填寫學經歷',

        ];
    }
    public function getTeacherValues($updated_by,$removed)
    {
        $values=$this->get('teacher');   
        $values= Helper::setUpdatedBy($values,$updated_by);
        if(intval($values['reviewed']) >0 ){
            $values['reviewed_by']=$updated_by;
        }
        return Helper::setRemoved($values,$removed);
        
    }
}
