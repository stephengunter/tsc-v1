<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\Helper;

class LessonRequest extends FormRequest
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
        if($this->isDayOff()){
            return [];
        }
        return [
            'lesson.order' =>'required',
            'lesson.on' =>'required',
            'lesson.off' =>'required',
            'lesson.teachers'  => 'required',
        ];
    }
    public function messages()
    {
        return [
            'lesson.order.required' => '必須選擇順序',
            'lesson.teachers.required' => '必須選擇授課教師',
            'lesson.on.required' => '必須選擇上課時間',
            'lesson.off.required' => '必須選擇下課時間',
        ];
    }

    public function isDayOff()
    {
        $status=(int)$this['lesson']['status'];
        return $status < 0 ; 
    }
    public function getValues($updated_by,$removed)
    {
        $values=array_except($this['lesson'], ['course','teachers', 'volunteers']);
        $values= Helper::setUpdatedBy($values,$updated_by);
        return Helper::setRemoved($values,$removed);
    }
    public function getTeacherIds()
    {
        $teachers=$this['lesson.teachers'];
        if(empty($teachers)) return [];
      
        $teacherIds=array_pluck($teachers, 'value');
           return    $teacherIds;     
     
    }
    public function getVolunteerIds()
    {
        $volunteers=$this['lesson.volunteers'];
        if(empty($volunteers)) return [];
      
        $volunteerIds=array_pluck($volunteers, 'value');
           return    $volunteerIds;     
     
    }
}
