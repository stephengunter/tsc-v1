<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Support\Helper;

class ClassTime extends Model
{
    protected $fillable = ['course_id', 'weekday_id', 'on', 'off','updated_by'];

	
    public function course() {
		return $this->belongsTo('App\Course');
	}
	public function weekday() {
		return $this->belongsTo('App\Weekday');
	}

	public static function initialize($course_id,$weekday_id)
    {
        return [
			 'course_id' => $course_id,
			 'weekday_id' => $weekday_id,
		     'on' =>  1600,
			 'off' =>  1800,
        ];
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

	public function fullText() {
		$day= $this->weekday->text ;

		$on=Helper::convertTimeNumberToText($this->on);
		$off=Helper::convertTimeNumberToText($this->off);

		return $day . ' ' . $on . ' - ' . $off ;
	}
}
