<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
	protected $fillable = ['course_id', 'order', 'title', 
	'content','materials','updated_by'];

    public function course() {
		return $this->belongsTo('App\Course');
	}

	public static function initialize($course_id)
    {
		$order=Schedule::where('course_id',$course_id)->max('order');
		if(!$order) $order=1;
		else $order+=1;
        return [
			 'course_id' => $course_id,
			 'order' => $order,
        ];
    }

	public function canEditBy($user)
	{
        if(!$this->course) return $user->isAdmin();
		return $this->course->canEditBy($user);
          
	} 
	public function canDeleteBy($user)
	{
		if(!$this->course) return $user->isAdmin();
       	return $this->course->canDeleteBy($user);
        
	}

	 
}
