<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Profile;
use App\Role;
use App\Center;

use App\Support\Helper;

use App\Support\FilterPaginateOrder;

class Admin extends Model
{
    use FilterPaginateOrder;
    protected $filter = [
        'role', 'updated_at' , 'user.profile.fullname'
    ];


	protected $primaryKey = 'user_id';
	protected $fillable = ['role', 'active', 'removed','updated_by' ];			 
    
    public static function initialize()
    {
        return [ 
                 'active' => 1,
                 'center_id' => 0
               ];      
       
    }
    


    public function centers()
    {
        return $this->belongsToMany('App\Center','center_admin','admin_id','center_id');
    }
	public function user() {
		return $this->belongsTo('App\User');
	}
    public static function roleName()
    {
        return Role::adminRoleName();
    }
	public function roleModel()
	{
		if(!$this->role) return null;
		return Role::where('name',$this->role)->first();
	}

    public function addToRole()
    {
        $user=$this->user;
        $roleName=$this->role;
        if(!$user->hasRole($roleName)){
            $role=Role::where('name',$roleName)->first();
            $user->attachRole($role);       
        } 

        $user=$this->user;
        if($roleName==Role::adminRoleName()){
            $role=Role::where('name',Role::ownerRoleName())->first();
            $user->detachRole($role); 
        }else{
            $role=Role::where('name',Role::adminRoleName())->first();
            $user->detachRole($role); 
        }
    }

	public function removeRole()
	{
        
        $roleName=Role::adminRoleName();	
			
		if($user->hasRole($roleName)){
            $role=Role::where('name',$roleName)->first();
            $user->detachRole($role);       
        } 
		$roleName=Role::ownerRoleName();		
		if($user->hasRole($roleName)){
            $role=Role::where('name',$roleName)->first();
            $user->detachRole($role);       
        } 
	}
    
    public function isHeadCenterBoss()
    {
        if(!$this->user->isOwner()) return false;
        return $this->fromHeadCenter();
    }
    public function fromHeadCenter()
    {
        if(!$this->centers) return false;
        $head=Center::getHeadCenter()->first();
        return $this->centers->contains($head);
    }
	
	public function canViewBy($user)
	{
		if($user->id==$this->user_id) return true;
		return $user->isOwner();
	}
	public function canEditBy($user)
	{
		if(!$user->isOwner()) return false;

        if($user->admin->fromHeadCenter()) return true;
		if(!$user->admin->centers()->count()) return false;

		return Helper::centersIntersect($this ,$user->admin);	
          
	}
	public function canDeleteBy($user)
	{
		return $this->canEditBy($user);
	}
	public function isValid()
    {
        return !$this->removed;
      
    }

    public function centersCanAdmin()
	{
        if($this->isHeadCenterBoss()){
            return Center::where('removed',false)->get();
        }

        return $this->validCenters();
	}

    public function canAdminCenter($center)
	{
        if(!$center->isValid()) return true;
        return $this->centers->contains($center);
	}

	public function validCenters()
	{
		 return Helper::validCenters($this);
	}

    public function defaultCenter()
    {
          if(!$this->validCenters()) return null;

          $sorted = $this->validCenters()->sortBy('head');
          
          return $sorted->values()->all()[0];
    }

	public function centersCanAdd()
	{
        return Helper::centersCanAdd($this);
	}
    public function centersCanAddByAdmin($admin)
	{
         return Helper::centersCanAddByAdmin($this,$admin);
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
