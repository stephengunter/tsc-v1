<?php

namespace App\Http\Requests\Notice;

use Illuminate\Foundation\Http\FormRequest;
use App\User;
use App\Notice;
use App\Support\Helper;

class NoticeRequest extends FormRequest
{
    public function authorize()
    {
        
        return true;
    }

    public function rules()
    {  
        $rules= [
             'notice.title'=> 'required',
             'notice.content'=> 'required',
        ];

        return $rules;
       
    }

    public function getId()
    {
        $values=$this->get('notice');   
        $id=0;        
        if(array_key_exists ( 'id' ,$values)){
            $id=(int)$values['id'];
        }  
        return $id;
    }


    public function messages()
    {
        return [

            'notice.title.required' => '必須填寫標題',
            'notice.content.required' => '必須填寫內容',
        ];

        
    }
    public function getFiles()
    {
        return $this->get('attachments');
        
    }
    public function getValues($updated_by=null,$removed=null)
    {
        $values=$this->get('notice');   
        if($updated_by)  $values= Helper::setUpdatedBy($values,$updated_by);
        if($removed) $values=Helper::setRemoved($values,$removed);
       
        return $values;
        
    }
}
