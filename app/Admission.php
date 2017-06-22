<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Admission extends Model
{
    protected $primaryKey = 'course_id';
    
    protected $fillable = [
	   'updated_by'
	];

    public function course() {
		return $this->belongsTo('App\Course');
	}
    public function admits()
	{
		return $this->hasMany('App\Admit','course_id');
	}

	public function canDeleteBy($user)
	{
		if($this->admits) return false;
        return $this->course->canDeleteBy($user);
        
	}

    public function canEditBy($user)
    {
        return $this->course->canEditBy($user);
        
    }
    
}
