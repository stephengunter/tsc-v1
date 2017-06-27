<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\Helper;
use App\Lesson;

use Carbon\Carbon;

class LeaveRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    
    public function rules()
    {
        return [
            'leave.user_id' =>'required',
        ];
    }
    public function messages()
    {
        return [
            'leave.user_id.required' => '必須選擇姓名',
        ];
    }
    public function getLessonId()
    {
        $values=$this['leave'];
        return $values['lesson_id'];
    }
   
    public function getValues($lesson ,$updated_by)
    {
        $values=$this['leave'];
        $values= Helper::setUpdatedBy($values,$updated_by);
        
        $begin_time=Helper::getHourMinute($values['begin_at']);
        $begin_at=Carbon::parse($lesson->date);
        $begin_at->hour=$begin_time['hour'];
        $begin_at->minute =$begin_time['minute'];

        $values['begin_at']= $begin_at->toDateTimeString();

        $end_time=Helper::getHourMinute($values['end_at']);
        $end_at=Carbon::parse($lesson->date);
        $end_at->hour=$end_time['hour'];
        $end_at->minute =$end_time['minute'];

        $values['end_at']= $end_at->toDateTimeString(); 

        return $values;
    }

    public function check($lesson)
    {
        $values=$this['leave'];
        $begin_at=(int)$values['begin_at'];
        $end_at=(int)$values['end_at'];

        if($begin_at >= $end_at) {
           return [
                'leave.time' =>  ['時間錯誤'] 
             ];
        
        }
        
        if($begin_at < (int)$lesson->on){
           return [
                'leave.time' =>  ['時間錯誤(起始時間早於課程時間)'] 
            ];
           
        }
        if($end_at > (int)$lesson->off){
            return [
                'leave.time' =>  ['時間錯誤(結束時間大於課程時間)'] 
            ];
        }

        
        
        return [];

        $errors=[];
         return  [
                   'begin_date' => ['起始日期與上課時間不符'] 
                 ] ;   
         return   response()->json([
                         'leave.time' => ['缺少上課時間'] 
                      ]  ,  422);   
    }
    
}
