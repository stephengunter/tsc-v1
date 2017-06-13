<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
	
	protected $fillable = ['name', 'center_id', 'active' ,
	                       'removed' ,'updated_by' , 'ps'];

    public static function initialize()
    {
        return [
			 'name' => '',
			 'active' => 1,
			 'center_id' => '',
			 'ps' => '',
        ];
    }
	public function lessons() 
	{
		return $this->hasMany('App\Lesson');
	}
	public function center() 
	{
		return $this->belongsTo('App\Center');
	}
    public function isValid()
	{
		return !$this->removed;
	}
   
	public function validLessons()
	{
		if(!$this->lessons()->count()) return null;
        return $this->lessons()->where('removed',false)->get();
	}

	public function canEditBy($user)
	{
		if(!$user->isAdmin()) return false;
		if(!$this->isValid()) return true;
		
		$center=$this->center;
		if(!$center) return true;

		return $center->canEditBy($user);
          
	} 
	public function canDeleteBy($user)
	{
		if(!$this->canEditBy($user)) return false;
		if(count($this->validLessons())){
			return false;
		}else{
			 return true;
		}
	}

	
}
