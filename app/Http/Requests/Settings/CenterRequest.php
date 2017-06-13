<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\Helper;

class CenterRequest extends FormRequest
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
            'center.name' => 'required|max:255',
        ];
    }
    public function messages()
    {
        return [
            'center.name.required' => '必須填寫名稱',
        ];
    }

    public function getValues($updated_by,$removed)
    {
        $except=['contact_info','photo_id'];

        $values=array_except($this->get('center'), $except);
        $values= Helper::setUpdatedBy($values,$updated_by);
        return Helper::setRemoved($values,$removed);
    }
}
