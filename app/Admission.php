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
    
}
