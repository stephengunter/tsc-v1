<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Teacher;
use App\Course;
use Carbon\Carbon;

class Lesson extends Model
{
	// protected $fillable = ['course_id', 'classroom_id', 'order', 'status' ,
	// 						'date','on', 'off' , 'title' , 'content', 'materials' ,'ps'	];

    protected $guarded = [];


	public static function initialize($course)
    {
        $order=1;
        if($course->lessons()->count()){
            $order=$course->lessons()->max('order') + 1;
        }
        return [    
                'course_id' => $course->id,
                'classroom_id' => 0,
                'order' => $order,
                'date'=>Carbon::now()->toDateString(),
                'status' => 0,
                'on' =>  1600,
                'off' =>  1800,
            
            ];
    }                   

    public function course() {
		return $this->belongsTo('App\Course');
	}
	 public function classroom() {
		return $this->belongsTo('App\Classroom');
	}

    public function lessonParticipants() {
		return $this->hasMany('App\LessonParticipant');
	}

    public function hasTeacher($teacher) 
    {
		$ids =$this->teacherIds();       
        if(empty($ids))  return false;

        return in_array($teacher, $ids);
	}

    public function teachers() 
    {
		$ids =$this->teacherIds();
        if(empty($ids))  return null;
        return Teacher::whereIn('user_id', $ids)->get();
	}
    public function teacherIds() 
    {
        if(!$this->lessonParticipants()->count()) return [];
		$ids =$this->lessonParticipants()->where('role','Teacher')->get()->pluck('user_id')->toArray();;
        return $ids;
	}
    public function hasVolunteer($volunteer) 
    {
		$ids =$this->volunteerIds();
        if(empty($ids))  return false;
        return in_array($volunteer, $ids);
	}
    public function volunteers() 
    {
        $ids =$this->volunteerIds();
        if(empty($ids))  return null;
        return User::with('profile')->whereIn('id', $ids)->get();
	}
    public function volunteerIds() 
    {
        if(!$this->lessonParticipants()->count()) return [];
		$ids =$this->lessonParticipants()->where('role','Volunteer')->get()->pluck('user_id')->toArray();;
        return $ids;
	}

    public function canEditBy($user)
	{
        if(!$this->course) return $user->isAdmin();
		return $this->course->canEditBy($user);
          
	} 
	public function canDeleteBy($user)
	{
	  
       	return $this->canEditBy($user);
        
	}

     
}
