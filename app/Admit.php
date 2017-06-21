<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Support\FilterPaginateOrder;

class Admit extends Model
{
	   use FilterPaginateOrder;
		 protected $filter =  ['id'];

     public function admission() 
		 {
			 	return $this->belongsTo('App\Admission');
	   }
     public function signup()
		 {
		     return $this->belongsTo('App\Signup');
	   }
}
