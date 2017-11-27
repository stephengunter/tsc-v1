<?php

namespace App;

use App\Course;

use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
	protected $fillable = [  'year', 'order', 'name',
							 'number','open_date', 'bird_date','close_date',
							 'active','removed', 'updated_by'
							
						  ];
	public static function canEdit($user)
	{
		if($user->isDev()) return true;
		
		if(!$user->isAdmin()) return false;
		
		return 	$user->admin->fromHeadCenter();
	}

	

	public static function initialize()
    {
        return [
             'year' => '', 
			 'order' => '',
			 'name' => '',
			 'open_date' => '',
			 'bird_date' => '',
			 'close_date' => '',
			 'active' => 1
        ];
    }
	
    public function courses() {
		return $this->hasMany(Course::class);
	}

	

	public function validCourses()
	{
		if(!$this->courses()->count()) return null;
        return $this->courses()->where('removed',false)->get();
	}

	public function canEditBy($user)
	{
		return  static::canEdit($user);
          
	}
	public function canDeleteBy($user)
	{
		if($this->canDelete()){
			return $this->canEditBy($user);
		}else{
			return false;
		}
		
	}

	public function canDelete()
	{
		if(count($this->validCourses())) return false;
         return  true;
	}

	
}
