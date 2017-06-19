<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Support\FilterPaginateOrder;

class Category extends Model
{
   use FilterPaginateOrder;

    protected $guarded = [];

	// protected $fillable = ['name', 'parent', 'order', 'active'	, 'icon'];

    // whitelist of filter-able columns
    protected $filter = [
        'name', 'order','active'
    ];

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
        if(!$this->courses()->count()) return null;
        return $this->courses()->where('removed',false)->get();
    }

    public function canDelete()
    {
        if(count($this->validCourses())) return false;
         return  true;
    }
    public function canEditBy($user)
	{
		return $user->isAdmin();
          
	} 
	public function canDeleteBy($user)
	{
        if(!$this->canDelete()) return  false;
        
        return $this->canEditBy($user);
	}



    
}
