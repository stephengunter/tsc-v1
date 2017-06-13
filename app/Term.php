<?php

namespace App;

use App\Course;

use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
	protected $fillable = [  'year', 'order', 'name',
							 'number','active','removed',
							 'updated_by'
						  ];
	
	public static function initialize()
    {
        return [
             'year' => '', 
			 'order' => '',
			 'name' => '',
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
        return $user->isAdmin();
          
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
