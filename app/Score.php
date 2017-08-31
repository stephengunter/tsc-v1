<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    protected $fillable = ['student_id', 'title', 'ps', 
       'points', 'updated_by'
    ];

    public function student() 
	{
		 return $this->belongsTo('App\Student');
	}
}
