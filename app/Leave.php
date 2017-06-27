<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Leave extends Model
{
    protected $fillable = [ 'user_id', 'lesson_id', 'type_id', 'ps',
                                
                            'begin_at' ,'end_at' , 'updated_by'   
                            
                            ];
    public static  function initialize($lesson)
    {
        return [
           'user_id' =>'',
           'lesson_id' =>$lesson->id,
           'type_id'=>0,
           'begin_at' => $lesson->on,
           'end_at' => $lesson->off,
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
    public function typeName() 
    {
        if($this->type) return $this->type->name;

        return '';
  	}
    public function canEditBy($user)
	{
        if(!$this->lesson) return $user->isAdmin();
		return $this->lesson->canEditBy($user);
          
	} 
	public function canDeleteBy($user)
	{
       	return $this->canEditBy($user);
        
	}

    public function hours()
	{
       	$begin = Carbon::parse($this->begin_at);
        $end = Carbon::parse($this->end_at);

        return $end->diffInHours($begin);
        
	}
}
