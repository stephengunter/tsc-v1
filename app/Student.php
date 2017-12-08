<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Support\FilterPaginateOrder;

class Student extends Model
{
    use FilterPaginateOrder;
	protected $filter =  ['user.profile.fullname'];
	protected $fillable = ['course_id', 'user_id', 'number',
							'join_date' , 'out_date', 'active', 'ps',
							'updated_by'

							];

	
    // public function register() 
	// {
	// 	 return $this->belongsTo('App\Register','course_id');
	// }
	public function course()
	{
		 return $this->belongsTo('App\Course');
	}
    public function user()
	{
		 return $this->belongsTo('App\User');
	}
	public function scores()
	{
		return $this->hasMany('App\Score');
	}

	public function canDeleteBy($user)
	{

        return $this->register->course->canEditBy($user) ;
	}
    public function canEditBy($user)
	{
       return $this->register->course->canEditBy($user) ;
	}

	public function getScore()
	{
		$this->score=null;
		if(count($this->scores)) {
			$this->score=$this->scores[0];
		} 
		return $this->score;
	}
	public function getName()
	{
		$this->name=$this->user->profile->fullname;
		return $this->name;
	}
}
