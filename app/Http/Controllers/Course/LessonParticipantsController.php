<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

use App\Repositories\Courses;
use App\Repositories\Signups;
use App\Repositories\LessonParticipants;


use App\Role;
use App\Lesson;
use App\LessonParticipant;
use App\Course;
use App\Student;

use App\Support\Helper;
use App\Http\Middleware\CheckAdmin;


use DB;


class LessonParticipantsController extends BaseController
{
    public function __construct(Courses $courses,CheckAdmin $checkAdmin) 
    {
       
		$exceptAdmin=[];
        $allowVisitors=[];
        $this->setMiddleware( $exceptAdmin, $allowVisitors);
        
        $this->courses=$courses;

        $this->setCheckAdmin($checkAdmin);
		
	}

    public function index()
    {
        $request = request();
        $lesson_id=(int)$request->lesson; 
        $role=$request->role; 
        $role=strtolower($role);
        
        $current_user=$this->currentUser();

        if($role==strtolower(Role::teacherRoleName()))
        {

        }

        $lesson=Lesson::findOrFail($lesson_id);
        if($lesson->canEditBy($current_user)){
            $updated_by=$current_user->id;
            $lesson->checkStudents($updated_by);
        }
        
        $studentList=$lesson->students()->with('user.profile') -> filterPaginateOrder();
       
        return response()
            ->json([
                'model' => $studentList
            ]);

    }
    public function update(Request $request, $id)
    {
        $current_user=$this->currentUser();
        $record=LessonParticipant::findOrFail($id);
       
        if(!$record->canEditBy($current_user)){
            return  $this->unauthorized(); 
        }

        $record->ps=$request['ps'];
        $record->updated_by=$current_user->id;
        $record->save();
        
        return response() ->json([ 'record' => $record ]);   
    }
   
   
}
