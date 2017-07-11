<?php

namespace App\Http\Requests\Signups;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\Helper;

class SignupUserRequest extends FormRequest
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
        $id=$this->getUserId();
        return [
              'user.profile.fullname' => 'required|max:255',
              'user.profile.SID' =>'required|unique:profiles,SID,'.$id .',user_id',
			  'user.phone' =>'required|min:6|unique:users,phone,'.$id,
        ];
    }
    public function messages()
    {
        return [
                

				'user.phone.required' => '必須填寫手機號碼',
				'user.phone.min' => '手機號碼格式不正確',
				'user.phone.unique' => '手機號碼與現存使用者重複',

				'user.profile.fullname.required' => '必須填寫姓名',

				'user.profile.SID.required' => '必須填寫身分證號',
				'user.profile.SID.unique' => '身分證號重複',
				
        ];
    }
    public function getUserId()
    {
        $userValues = $this['user'];
        $user_id=0;        
        if(array_key_exists ( 'id' ,$userValues)){
            $user_id=(int)$userValues['id'];
        }  
        return $user_id;
    }
    public function getUserValues($updated_by,$removed)
    {
        $request=$this->get('user');
        $values=array_except($request, ['profile','contact_info','role']);
       
        $values= Helper::setUpdatedBy($values,$updated_by);
        return Helper::setRemoved($values,$removed);
    }
    public function getProfileValues($updated_by)
    {
        $request=$this->get('user');        
        $values=array_except($request['profile'], ['photo_id']);
        return Helper::setUpdatedBy($values,$updated_by);
        
    }
    
}
