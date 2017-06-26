<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    public function type() 
    {
		return $this->belongsTo('App\LeaveType','type_id');
      
	}
    public function user() 
    {
		return $this->belongsTo('App\User');
      
	}
    public function course() 
    {
		return $this->belongsTo('App\Course');
      
	}
}
