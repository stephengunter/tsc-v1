<?php

namespace App;

use App\Center;
use App\Teacher;

use Illuminate\Database\Eloquent\Model;
use App\Support\FilterPaginateOrder;
use Illuminate\Notifications\Notifiable;

use App\Support\Helper;
use App\Role;

class Teacher extends Model
{
	
    use FilterPaginateOrder;
    use Notifiable;

    protected $primaryKey = 'user_id';
    protected $guarded = [];

	
    protected $filter = [
         'user.profile.fullname','specialty', 'active','reviewed', 'education','updated_at'
    ];
    
 
    
    public function user() {
		return $this->belongsTo('App\User');
	}

    public function courses()
    {
        return $this->belongsToMany('App\Course','course_teacher','course_id','teacher_id');
    }

    public function centers()
    {
        return $this->belongsToMany('App\Center','center_teacher','teacher_id','center_id');
    }

    public static function roleName()
    {
        return Role::teacherRoleName();
    }

    public function getName()
    {
        return $this->user->profile->fullname;
    }
    public function getPhoto()
    {
        return $this->user->profile->photo();
    }

    public function addToRole()
    {
        $user=$this->user;
        $roleName=$this->roleName();	
        if(!$user->hasRole($roleName)){
            $role=Role::where('name',$roleName)->first();
            $user->attachRole($role);       
        } 
    }

	public function removeRole()
	{
		$roleName=$this->roleName();		
		if($this->user->hasRole($roleName)){
            $role=Role::where('name',$roleName)->first();
            $this->user->detachRole($role);       
        } 
		
	}
    public function validCenters()
	{
		 return Helper::validCenters($this);
	}

    public function centersCanAdd()
	{
        return Helper::centersCanAdd($this);
	}
    public function centersCanAddByAdmin($admin)
	{
         return Helper::centersCanAddByAdmin($this,$admin);
    }

    public function isValid()
    {
        return ($this->active && !$this->removed && $this->reviewed);
      
    }
    public function attachCenter($center_id)
    {
            if(!$this->centers->contains($center_id)) 
            {
                $this->centers()->attach($center_id);
            }
           
    }
    public function detachCenter($center_id)
    {
            if($this->centers->contains($center_id)) 
            {
                $this->centers()->detach($center_id);
            }
    }
    

    

    public function centerNames()
    {
        if(count($this->validCenters())){
            return $this->validCenters()->pluck('name')->toArray();
        }else{
            return '';
        }
        
    }
    public function canViewBy($user)
	{
		if($user->id==$this->user_id) return true;
		return $user->isAdmin();
	}
	
	public function canEditBy($user)
	{
		if($user->id==$this->user_id) return true;

        if(!$user->isAdmin()) return false;
        return Helper::centersIntersect($this,$user->admin);
          
	}
	public function canDeleteBy($user)
	{
		return $this->canEditBy($user);
	}

    public function experiencesArray()
    {
       
        return   explode('<br>', $this->experiences);
    }
  
}
