<?php

namespace App;

use App\Center;
use App\Support\FilterPaginateOrder;
use Illuminate\Database\Eloquent\Model;

use App\Support\Helper;
use App\Role;

class Volunteer extends Model
{
	use FilterPaginateOrder;

	protected $primaryKey = 'user_id';
    protected $fillable = ['join_date', 'active', 'removed','updated_by' ];
						 

	protected $filter = ['join_date','user.profile.fullname'];

    public static function initialize()
    {
        return [ 'join_date' => '',
                 'active' => 1,
               ];      
       
    }
    public function centers()
    {
        return $this->belongsToMany('App\Center','center_volunteer','user_id','center_id');
    }
	public function user() {
		return $this->belongsTo('App\User');
	}
	public static function roleName()
    {
        return Role::volunteerRoleName();
    }
    public function getName()
    {
        return $this->user->profile->fullname;
    }
    public function addToRole()
    {
        $user=$this->user;
        
        if(!$user->hasRole($this->roleName())){
            $role=Role::where('name',$this->roleName())->first();
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
	public function isValid()
    {
        return !$this->removed;
      
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
    public function syncCenters($centerIds)
    {
        $this->centers()->sync($centerIds);
    }
}
