<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\Helper;

class TermRequest extends FormRequest
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
            'term.open_date' => 'required',
            'term.bird_date' => 'required',
            'term.close_date'=> 'required', 
        ];
    }
    public function messages()
    {
        return [
            'term.open_date.required' => '必須填寫報名起始日',
            'term.bird_date.required' => '必須填寫早鳥優惠截止日',
            'term.close_date.required' => '必須填寫報名截止日',     
        ];
    }

    public function getId()
    {
        $values = $this['term'];
        $id=0;        
        if(array_key_exists ( 'id' ,$values)){
            $id=(int)$values['id'];
        }  
        return $id;
    }
    public function getValues($updated_by,$removed)
    {
       
        $values=array_except($this->get('term'), ['canDelete']); 
        $values['number']=$values['year'] . $values['order'];
        $values['name']=$values['number'];
        $values= Helper::setUpdatedBy($values,$updated_by);
        return Helper::setRemoved($values,$removed);
        
    }
}
