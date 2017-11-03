<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Course;

use App\Repositories\Courses;
use App\Repositories\Teachers;
use App\Repositories\Categories;
use App\Repositories\Terms;
use App\Repositories\Centers;
use App\Repositories\Weekdays;

use App\Http\Requests\Course\SignupInfoRequest;

use App\Support\Helper;

use App\Events\CourseUpdated;

class SignupInfoesController extends BaseController
{
    
    public function __construct(Courses $courses)                           
    {
        $this->courses=$courses;
	}
    
    
    public function show($id)
    {
        $current_user=$this->currentUser();
        $course = Course::findOrFail($id);
        $course->canEdit=$course->canEditBy($current_user);

        return response()->json(['signupinfo' => $course]);          
               
    }
    public function edit($id)
    {
        $course = Course::findOrFail($id);   
        $current_user=$this->currentUser();
        if(!$course->canEditBy($current_user)){
            return  $this->unauthorized(); 
        }

        $course->open_date=Helper::checkDateString($course->open_date);
        $course->close_date=Helper::checkDateString($course->close_date);
       
        return response()->json(['signupinfo' => $course]);  
    }
    public function update(SignupInfoRequest $request, $id)
    {
        $course = Course::with('center')->findOrFail($id);     
        $current_user=$this->currentUser();
        if(!$course->canEditBy($current_user)){
            return  $this->unauthorized();         
        }

        $removed=false;
        $updated_by=$current_user->id;

        $values=$request->getValues($updated_by,$removed);

        $course->update($values);
        event(new CourseUpdated($course, $current_user));

        return response()->json(['signupinfo' => $course]); 
    }
    
}
