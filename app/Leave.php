<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected $fillable = [ 'user_id', 'lesson_id', 'type_id', 'ps',
                                
                            'begin_at' ,'end_at' , 'updated_by'   
                            
                            ];
    public static  function initialize()
    {
        return [
           'user_id' =>'',
           'lesson_id' =>'',
           'type_id'=>0,
           'begin_at' => '',
           'end_at' => '',
           'ps' => '',
        ];
    }
    public function type() 
    {
		    return $this->belongsTo('App\LeaveType','type_id');
      
  	}
    public function user() 
    {
		    return $this->belongsTo('App\User');
      
	  }
    public function lesson() 
    {
		    return $this->belongsTo('App\Lesson');
      
   	}
}
