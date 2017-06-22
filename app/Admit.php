<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Support\FilterPaginateOrder;

class Admit extends Model
{
	use FilterPaginateOrder;
	protected $filter =  ['id'];

	protected $fillable = [
		'course_id'	, 'signup_id' , 'updated_by'	
	];

	
    public function admission() 
	{
		 return $this->belongsTo('App\Admission','course_id');
	}
    public function signup()
	{
		 return $this->belongsTo('App\Signup');
	}

	public function canDeleteBy($user)
	{
       return $this->admission->course->canEditBy($user) ;
	}
}
