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
        $rules= [
            'course.name' => 'required|max:255',
            'course.begin_date'=> 'required',
            'course.end_date' => 'required',  
                         
        ];
       
        $extraRules=[];
        $courseValues=$this->get('course');
        
        //是否為學分班
        $isCredit=$courseValues['isCredit'];
        if($isCredit){
            $extraRules=[
                'course.credit_count' => 'integer|min:1' , 
                'course.credit_price' => 'required|numeric',
            ];
        }

        $rules=array_merge($rules,$extraRules);

        $group=$courseValues['group'];
        if($group){
            //群組課程
            $parent=(int)$courseValues['parent'];
            $extraRules=$this->groupCourseRules($parent);
        }else{
            //一般課程
            $extraRules=$this->normalCourseRules();
        }

        return array_merge($rules,$extraRules);

    }

    private function normalCourseRules()
    {
        return [
            'course.hours' => 'required|numeric',
            'course.teachers'  => 'required',
            'course.categories' => 'required',
        ];
    }
    private function groupCourseRules(int $parent)
    {
        if($parent){
            //子課程, 可不填分類,教師必填,時數必填
            return [
                'course.hours' => 'required|numeric',
                'course.teachers'  => 'required',
            ];
        }else{
            //群組root課程,可不填教師,分類必填
            return [
                'course.categories' => 'required',
            ];
            
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

            'course.credit_count.min' => '學分數必須大於0',
            'course.credit_price.required' => '必須填寫學分單價',
            'course.credit_price.numeric' => '學分單價必須為數字',
         

            'course.categories.required' => '必須選擇課程分類',
            'course.teachers.required' => '必須選擇教師',
         
        ];
    }

    public function isGroup()
    {
        $courseValues=$this->get('course');
        return $courseValues['group'];
    }
    public function getCourseId()
    {
        $courseValues=$this->get('course');
        if (array_key_exists('id', $courseValues)) return $courseValues['id'];
        return 0;
        
    }
    public function getCourseNumber()
    {
        $courseValues=$this->get('course');
        return $courseValues['number'];
        
    }

    public function getCourseValues($updated_by='',$removed=false)
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
