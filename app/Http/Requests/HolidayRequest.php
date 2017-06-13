<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\Helper;

class HolidayRequest extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'holiday.name' => 'required',
            'holiday.date' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'holiday.name.required' => '必須填寫名稱',
            'holiday.date.required' => '必須選擇日期',
        ];
    }
    public function getValues($updated_by)
    {
        $values=$this->get('holiday');
         $values= Helper::setUpdatedBy($values,$updated_by);
        return $values;
    }
}
