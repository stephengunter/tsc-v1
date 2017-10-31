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
	
	protected $fillable = ['name', 'code' , 'active', 'display_order',
						   'contact_info','removed','updated_by'];
	
    protected $filter = [
        'name', 'code' ,'updated_at','display_order'
	];
	
	public function setcodeAttribute($value) {
		
		$this->attributes['code'] = strtoupper($value);
	}

	public static function initialize()
    {
         return [
			 'name' => '',
			 'active' => 0,
			 'display_order' => -1
			 
        ];
	}

	
	
	public static function canEdit($user)
	{
		if($user->isDev()) return true;

		if(!$user->isAdmin()) return false;
		
		
		return 	$user->admin->fromHeadCenter();
          
	} 
	public static function canImport($user)
	{
		
		return 	static::canEdit($user);
          
	} 

	
	public static function getHeadCenter()
	{
		return static::where('head',true)->where('removed',false)->first();
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
		
		return 	static::canEdit($user);
          
	} 
	public function canDeleteBy($user)
	{
		if($this->head) return false;
		if($this->active) return false;
		return $this->canEditBy($user);
	}

	public function updateContactInfo($tel ,$fax, $zipcode, $street,$updated_by)
	{
		$contact_info=$this->contactInfo();
		
		if($contact_info){
			$contact_info->updateAddress($zipcode, $street,$updated_by);
			$contact_info->update([
				'tel' => $tel,
				'fax' => $fax,
				'updated_by' => $updated_by
			]);
        }else{
            $contact_info=ContactInfo::createByAddress($zipcode, $street,$updated_by);
            if($contact_info) {
                $this->contact_info=$contact_info->id;
                $this->updated_by=$updated_by;
                $this->save();
            }
        }
	}
	
}
