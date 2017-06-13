<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;

class CourseSearchRequest extends FormRequest
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
            'center' => 'required|numeric',
             'direction' => 'required|in:asc,desc',
         
            'course.begin_date'=> 'required',
            'course.end_date' => 'required',
             'course.hours' => 'required|numeric',

            'course.categories'  => 'required',
            'course.teachers'  => 'required',
        ];
       //  'column' => 'required|in:'.implode(',', $this->filter),
            'direction' => 'required|in:asc,desc',
            'per_page' => 'required|integer|min:1',
            'search_operator' => 'required|in:'.implode(',', array_keys($this->operators)),
            'search_column' => 'required|in:'.implode(',', $this->filter),
            'search_query_1' => 'max:255',
            'search_query_2' => 'max:255'
    }

    public function messages()
    {
        return [
            'center.required' => '錯誤的查詢參數',
             'center.numeric' => '必須填寫課程名稱',
            'course.begin_date.required' => '必須填寫開始日期',
            'course.end_date.required' => '必須填寫結束日期',
             'course.hours.required' => '必須填寫時數',
              'course.hours.numeric' => '時數必須為數字',

         

             'course.categories.required' => '必須選擇課程分類',
            'course.teachers.required' => '必須選擇教師',
         


        ];
    }
}
