<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Support\FilterPaginateOrder;
use App\ContactInfo;
use App\Photo;
use App\Repositories\Roles;

class Center extends Model
{
	use FilterPaginateOrder;
	
	protected $fillable = ['name', 'active'	, 'display_order','removed','updated_by'];
	
    protected $filter = [
        'name', 'updated_at','display_order'
    ];

	public static function initialize()
    {
         return [
			 'name' => '',
			 'active' => 1
			 
        ];
    }

    public function courses() 
	{
		return $this->hasMany('App\Course');
	}
	public function users()
    {
        return $this->belongsToMany('App\User','user_center');
    }

	public function contactInfo() 
	{
		if(!$this->contact_info) return null;
        return ContactInfo::find($this->contact_info);


	}

	public function classrooms() 
	{
		return $this->hasMany('App\Classroom');
	}

	

	public function volunteers() 
	{
		return $this->belongsToMany('App\Volunteer','center_volunteer','center_id','user_id');
	}
	public function teachers() 
	{
		 return $this->belongsToMany('App\Teacher','center_teacher','center_id','teacher_id');
		     
	}
	public function admins() 
	{
		 return $this->belongsToMany('App\Admin','center_admin','center_id','admin_id');
		     
	}

	public function addressText()
    {
       if($this->contactInfo()){
		   $addressA=$this->contactInfo()->addressA();
		  return $addressA->fullText();
	   }else{
		   return '';
	   }
    }
	public function phoneText()
    {
       if($this->contactInfo()){
		  return $this->contactInfo()->tel;
	   }else{
		   return '';
	   }
    }

	public function isValid()
	{
		return !$this->removed;
	}
	public function photo()
	{
		if(!$this->photo_id){
			return Photo::defaultCenter();
		}
		return Photo::find($this->photo_id);

	}


	public function canEditBy($user)
	{
		if(!$user->isAdmin()) return false;
		if(!$this->isValid()) return true;
		
		return 	$user->admin->centers->contains($this);
          
	} 
	public function canDeleteBy($user)
	{
		return $this->canEditBy($user);
	}
	
}
