<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\Helper;

class CourseRequest extends FormRequest
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
        $basicRules= [
            'course.name' => 'required|max:255',
         
            'course.begin_date'=> 'required',
            'course.end_date' => 'required',
           

           
         //   'course.teachers'  => 'required',
          
        ];
        

        $request=$this->get('course');
        $parent=(int)$request['parent'];
        $credit_count=(int)$request['credit_count'];

        if($credit_count){
            if($parent){
                $extraRules=[
                    'course.hours' => 'required|numeric',
                    'course.teachers'  => 'required',
                ];
                return array_merge($basicRules,$extraRules);
            }else{
               
                $extraRules=[
                    'course.categories' => 'required',
                ];
                return array_merge($basicRules,$extraRules);
            }
        }else{
            $extraRules=[
                'course.hours' => 'required|numeric',
                'course.teachers'  => 'required',
                'course.categories' => 'required',
            ];
            return array_merge($basicRules,$extraRules);
        }
        

        

        


    }

    public function messages()
    {
        return [
            'course.name.required' => '必須填寫課程名稱',
            'course.begin_date.required' => '必須填寫開始日期',
            'course.end_date.required' => '必須填寫結束日期',
             'course.hours.required' => '必須填寫時數',
              'course.hours.numeric' => '時數必須為數字',

         

             'course.categories.required' => '必須選擇課程分類',
            'course.teachers.required' => '必須選擇教師',
         
        ];
    }

    public function getCourseValues($updated_by,$removed)
    {
        $request=$this->get('course');
        $values=array_except($request, ['teachers','categories','center']);
        $values=array_except($values, ['photo_id']);
        $values= Helper::setUpdatedBy($values,$updated_by);
        return Helper::setRemoved($values,$removed);
    }
    public function getCategoryIds()
    {
         $request=$this->get('course');
        return array_pluck($request['categories'], 'value');
        
    }
    public function getTeacherIds()
    {
          $request=$this->get('course');
       return array_pluck($request['teachers'], 'value');
        
    }
}
