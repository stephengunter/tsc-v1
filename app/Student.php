<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Support\FilterPaginateOrder;

class Student extends Model
{
    use FilterPaginateOrder;
	protected $filter =  ['id'];

	protected $fillable = [
		'course_id'	, 'user_id' , 'updated_by'	
	];

	
    public function register() 
	{
		 return $this->belongsTo('App\Register','course_id');
	}
    public function user()
	{
		 return $this->belongsTo('App\User');
	}

	public function canDeleteBy($user)
	{
       return $this->register->course->canEditBy($user) ;
	}
    public function canEditBy($user)
	{
       return $this->register->course->canEditBy($user) ;
	}
}
