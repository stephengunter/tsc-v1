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
        $this->name= $this->user->profile->fullname;
        return $this->name;
    }
    public function getPhoto()
    {
        return $this->user->profile->photo();
    }

    public function getAccount() 
	{
		return $this->user->getAccount();
	}

    public function setAccount($number,$updated_by) 
	{
		return $this->user->setAccount($number,$updated_by);
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
    
    public function addGroupTeachers(array $teacher_ids)
    {
        if(!count($teacher_ids)) return;
        
        $current_ids=[];
        if($this->teacher_ids)  $current_ids=explode( ',', $this->teacher_ids);
       
       
        foreach($teacher_ids as $id){
            if(!in_array($id, $current_ids)){
                array_push($current_ids, $id); 
            }
        } 

        $this->teacher_ids=Helper::strFromArray($current_ids);
        $this->save();

        $current_ids=explode( ',', $this->teacher_ids);
        $center_ids=$this->centers->pluck('id')->toArray();
        foreach($center_ids as $center_id){
            foreach($current_ids as $id){
                $teacher=static::findOrFail($id);
                $teacher->attachCenter($center_id);
            } 
           
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
            if( in_array($user->id,$groupTeacherIds) ) return true;
        }

        if($user->isAdmin()){
            return Helper::centersIntersect($this, $user->admin);
        } 

        return false;
       
          
    }
    public function populateViewData()
    {
        $this->name=$this->getName();
        $this->centerNames=$this->centerNames();
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
