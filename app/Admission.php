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
		return $this->hasMany('App\Admit');
	}

	public function canDeleteBy($user)
	{
         return $this->course->canDeleteBy($user);
        
	}

    public function canEditBy()
    {
        return $this->course->canEditBy($user);
        
    }
    
}
