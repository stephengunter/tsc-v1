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
    
    
    public static function initialize()
    {
        return [
            'group' => 0,
            'experiences' => '',
            'certificate' => '',
            'specialty' => '',
            'active' => 0,
			'reviewed' => 0,
			'education' => '',
            'job' => '',
			'jobtitle' => '',
        ];
    }
    
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
    
    public function centersCanAddByUser($user)
	{
         return Helper::centersCanAddByUser($this,$user);
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
        if($user->isDev())  return true;
		return $user->isAdmin();
	}
	
	public function canEditBy($user)
	{
        if($user->isDev()) return true;
        if($user->id==$this->user_id) return true;
        
        if($this->group){
            $groupTeacherIds=$this->groupTeacherIds();
            return in_array($user->id,$groupTeacherIds);
        }

        if(!$user->isAdmin()) return false;
        return Helper::centersIntersect($this,$user->admin);
          
    }
    public function canReviewBy($user)
	{
        if($user->isDev()) return true;
        if(!$user->isOwner()) return false;
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

    public function groupTeacherIds()
    {
        if($this->teacher_ids){
            return explode( ',', $this->teacher_ids );
        }else{
            return [];
        }
    }

    public function updateReview($reviewed,$reviewed_by)
    {
         
         $this->reviewed=$reviewed;
         if($reviewed){
             $this->reviewed_by=$reviewed_by;
         }else{
             $this->reviewed_by='';
         }
         
         $this->save();

         return $this;
    }
  
}
