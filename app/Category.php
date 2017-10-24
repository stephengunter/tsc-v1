<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Support\FilterPaginateOrder;

class Category extends Model
{
   use FilterPaginateOrder;

    protected $guarded = [];

	// protected $fillable = ['name', 'parent', 'order', 'active'	, 'icon'];

    
    protected $filter = [
        'name', 'order','active'
    ];

    public static function canEdit($user)
    {
        if($user->isDev()) return true;

        if(!$user->isAdmin()) return false;

        return 	$user->admin->fromHeadCenter();
    }

    public function courses()
    {
        return $this->belongsToMany('App\Course','course_category');
    }
    public function notices()
    {
        return $this->belongsToMany('App\Notice','course_notice');
    }

    public function attachCourse($course_id)
    {
       if(!$this->courses->contains($course_id))
       {
           $this->courses()->attach($course_id);
       }
    }
    public function detachCourse($course_id)
    {
        if($this->courses->contains($course_id)) 
        {
            $this->courses()->detach($course_id);
        }
    }

    public function validCourses()
    {
        return $this->courses()->where('removed',false);
    }
    public function activeCourses()
    {
        return $this->validCourses()->where('active',true);
    }

    public function canDelete()
    {
        if(count($this->validCourses()->get())) return false;
         return  true;
    }
    public function canEditBy($user)
	{
		return 	static::canEdit($user);
          
	} 
	public function canDeleteBy($user)
	{
        if(!$this->canDelete()) return  false;
        
        return $this->canEditBy($user);
	}



    
}
