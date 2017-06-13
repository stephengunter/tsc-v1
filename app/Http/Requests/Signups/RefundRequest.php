<?php

namespace App\Http\Requests\Signups;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\Helper;

class RefundRequest extends FormRequest
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
             'refund.date' => 'required',
             'refund.courses_done' => 'required|numeric',
             'refund.tuition' => 'required|numeric',
             'refund.cost' => 'numeric',
             'refund.charge' => 'numeric',
          
        ];
    }

    public function messages()
    {
        return [
            'refund.date.required' => '必須填寫申請日期',
            
            'refund.courses_done.required' => '必須填寫已上課時數',
            'refund.courses_done.numeric' => '已上課時數必須為數字',

            'refund.tuition.numeric' => '可退學費必須為數字',
            'refund.cost.numeric' => '可退材料費必須為數字',
            'refund.charge.numeric' => '手續費必須為數字',
        ];
    }
    public function getValues($updated_by,$removed)
    {
        $values=$this->get('refund');
        
        $values= Helper::setUpdatedBy($values,$updated_by);
        return Helper::setRemoved($values,$removed);
    }
   
}
