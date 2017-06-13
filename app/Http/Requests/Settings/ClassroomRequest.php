<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\Helper;

class ClassroomRequest extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'classroom.name' => 'required|max:255',
        ];
    }
    public function messages()
    {
        return [
            'classroom.name.required' => '必須填寫名稱',
        ];
    }
    public function getValues($updated_by,$removed)
    {
        $values=array_except($this->get('classroom'), ['center','canDelete','canEdit']); 
        $values= Helper::setUpdatedBy($values,$updated_by);
        return Helper::setRemoved($values,$removed);
        
    }
}
