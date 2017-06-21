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

	public static function initialize($signup)
	{
        return [
			'course_id' => $signup->course_id,
			'signup_id' => $signup->id,
		];
	}

    public function admission() 
	{
		 return $this->belongsTo('App\Admission');
	}
    public function signup()
	{
		 return $this->belongsTo('App\Signup');
	}
}
