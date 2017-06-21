<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Admit extends Model
{
    public function admission() {
		return $this->belongsTo('App\Admission');
	}
    public function signup() {
		return $this->belongsTo('App\Signup');
	}
}
