<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Register extends Model
{
    protected $primaryKey = 'course_id';
    protected $fillable = [
	   'updated_by'
	];

    public function course() {
		return $this->belongsTo('App\Course');
	}
    public function students()
	{
		return $this->hasMany('App\Student','course_id');
	}

    public function canDeleteBy($user)
	{
        if(count($this->students)) return false;
        return $this->course->canDeleteBy($user);
        
	}

    public function canEditBy($user)
    {
        return $this->course->canEditBy($user);
        
    }

}
