<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\User;
use App\Support\Helper;

class AdminRequest extends FormRequest
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
          
            'user.phone' => 'required|max:255',
            'profile.fullname' => 'required|max:255',
            'centers'  => 'required', 
        ];
    }

    public function messages()
    {
        return [
           
            'user.phone.required' => '必須填寫手機號碼',
             'profile.fullname.required' => '必須填寫姓名',
            'centers.required'  => '必須選擇所屬中心',
            

        ];
    }
    public function getUserId()
    {
        return $this['user']['id'];
    }
    public function getUserValues($updated_by,$removed)
    {
         $values=$this->get('user');
        
        $values= Helper::setUpdatedBy($values,$updated_by);
        return Helper::setRemoved($values,$removed);

       
    }
    public function getProfileValues($updated_by)
    {
         $values=$this->get('profile');
        return Helper::setUpdatedBy($values,$updated_by);
        
        
    }
    public function getAdminValues($updated_by,$removed)
    {
        $values=$this->get('admin');   
        $values= Helper::setUpdatedBy($values,$updated_by);
        return Helper::setRemoved($values,$removed);
        
    }
    public function getCenterIds()
    {
        return array_pluck($this->get('centers'), 'value');
        
    }
}
