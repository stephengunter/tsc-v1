<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\Helper;

class CategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    
    public function rules()
    {
        $id=$this->getId();

        return [
            'category.name' => 'required|max:255',
            'category.code' => 'required|unique:categories,code,'. $id, 
        ];
    }
    public function messages()
    {
        return [
            'category.name.required' => '必須填寫名稱',
            'category.code.required' => '必須填寫代碼',
            'category.code.unique' => '代碼與現有分類重複',
        ];
    }
    public function getId()
    {
        $values = $this->get('category');
       
        $id=0;        
        if(array_key_exists ( 'id' ,$values)){
            $id=(int)$values['id'];
        }  
        return $id;
    }
    public function getValues($updated_by,$removed)
    {
        $values=$this->get('category');
        $values= Helper::setUpdatedBy($values,$updated_by);
        return Helper::setRemoved($values,$removed);
    }
}
