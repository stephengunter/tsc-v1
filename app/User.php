<?php

namespace App;

use App\Profile;
use App\Teacher;
use App\Volunteer;
use App\Photo;
use App\Role;
use App\ContactInfo;

use App\Support\FilterPaginateOrder;
use App\Support\Helper;

use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Authenticatable {
	use HasApiTokens;


	use FilterPaginateOrder;

	use EntrustUserTrait;
	use Notifiable;

	

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	
	protected $hidden = [
		'password', 'remember_token'
	];

	protected $fillable = ['name', 'email', 'password', 
						    'phone', 'updated_by','contact_info'
						  ];

    protected $filter = [
        'email', 'name', 'phone', 'profile.fullname','updated_at'
    ];
// 
	public static function getRules($email,$id,$role='User')
	{
		$basicRules=[];
        if($email){
            $basicRules= [
                 'user.name' => 'required|max:255',
                 'user.email'      => 'email|unique:users,email,'.$id,  
				 'profile.fullname'  => 'max:255',                
              ];
		}else{
              $basicRules= [
                 'user.name' => 'required|max:255',
                 'user.phone' => 'required|min:6|unique:users,phone,'.$id,
				 'profile.fullname'  => 'max:255',  
             ];
		}

		$extraRules=[]; 
		if($role =='User'){
			 return $basicRules;         
		}else if(in_array($role, Role::adminRoleNames())){
			$extraRules=[
			  'user.profile.fullname' => 'required|max:255',
			  'user.profile.dob' => 'required',
              'user.profile.SID' =>'required|unique:profiles,SID,'.$id .',user_id',
			  'user.phone' =>'required|min:6|unique:users,phone,'.$id,
		    ];
		}else if($role =='Teacher'){
			$extraRules=[
			  'user.profile.fullname' => 'required|max:255',
			  'user.profile.dob' => 'required',
              'user.profile.SID' =>'required|unique:profiles,SID,'.$id .',user_id',
			  'user.phone' =>'required|min:6|unique:users,phone,'.$id,
		    ];
		}else if($role =='Volunteer'){
			$extraRules=[
			  'user.profile.fullname' => 'required|max:255',
			  'user.phone' =>'required|min:6|unique:users,phone,'.$id,
		    ];
		}

		 

		 return array_merge($basicRules,$extraRules);

	}
	public static function getRuleMessages()
	{
		
         return [
				'user.name.required' => '必須填寫使用者名稱',
				
				'user.email.email' => 'Email格式不正確',
				'user.email.required' => '必須填寫Email',
				'user.email.unique' => 'Email與現存使用者重複',

				'user.phone.required' => '必須填寫手機號碼',
				'user.phone.min' => '手機號碼格式不正確',
				'user.phone.unique' => '手機號碼與現存使用者重複',

				'user.profile.fullname.required' => '必須填寫姓名',
				'user.profile.dob.required' => '必須填寫生日',
				'user.profile.SID.required' => '必須填寫身分證號',
				'user.profile.SID.unique' => '身分證號重複',
				

			];
	}
	

	public function setPasswordAttribute($value) {

		$this->attributes['password'] = bcrypt($value);
	}

    public static function initialize()
    {
        return [
             'email' => '', 
			 'name' => '',
			 'phone' => '',
		     'profile' =>  Profile::initialize()
        ];
    }



	public function profile() 
	{
		return $this->hasOne(Profile::class);
	}
	public function emailToken() 
	{
		return $this->hasOne(EmailToken::class);
	}
	public function passwordResetToken() 
	{
		return $this->hasOne(PasswordResetToken::class);
	}
	
	public function teacher()
	{
		return $this->hasOne(Teacher::class);
	}
	public function volunteer()
	{
		return $this->hasOne(Volunteer::class);
	}
	public function admin()
	{
		return $this->hasOne(Admin::class);
	}
	public function resume()
	{
		return $this->hasOne(Resume::class);
	}
	
	public function photoes()
	{
		return $this->hasMany(Photo::class);
	}
	public function signups() 
	{
		return $this->hasMany('App\Signup');
	}
	public function students() 
	{
		return $this->hasMany('App\Student');
	}

	public function getContactInfo() 
	{
		if(!$this->contact_info) return null;
		return ContactInfo::find($this->contact_info);
	}

	public function updateAddress($zipcode, $street,$updated_by)
    {
        
        $contact_info=$this->getContactInfo();
        if($contact_info){
            $contact_info->updateAddress($zipcode, $street,$updated_by);
        }else{
            $contact_info=ContactInfo::createByAddress($zipcode, $street,$updated_by);
            if($contact_info) {
                $this->contact_info=$contact_info->id;
                $this->updated_by=$updated_by;
                $this->save();
            }
        }
    }



	

	

	public function rolesCanAdd($with_admin=false)
	{
		$roles = Role::orderBy('importance','desc');
		if(!$with_admin){
			$adminRoleNames=Role::adminRoleNames();
			$roles = $roles->whereNotIn('name',$adminRoleNames);
		}
		if($this->roles->count()){
			$userRoleIds =$this->roles()->get()->pluck('id'); 
			return $roles->whereNotIn('id' , $userRoleIds)->get();

		}else{
          	return $roles->get();
		}
	}
	public function isDev()
	{
		if(!$this->roles->count()){
			return false;
		}
		
		if($this->hasRole('Dev')) return true;

		return false;
	}
	public function isAdmin()
	{
		if(!$this->roles->count()){
			return false;
		}
		
		if($this->hasRole(Role::adminRoleName())) return true;
		if($this->hasRole(Role::ownerRoleName())) return true;

		return false;
	}
	public function isOwner()
	{
		if(!$this->roles->count()){
			return false;
		}
		
		if($this->hasRole(Role::ownerRoleName())) return true;

		return false;
	}
	public function isTeacher()
	{
		if(!$this->roles->count()){
			return false;
		}
		
		if($this->hasRole(Role::teacherRoleName())) return true;

		return false;
	}
	public function isStudent()
	{
		if(count($this->students)) return true;
		return false;
	}
	public function isVolunteer()
	{
		if(!$this->roles->count()){
			return false;
		}
		
		if($this->hasRole(Role::volunteerRoleName())) return true;

		return false;
	}

	public function canViewBy($user)
	{
		if($user->id==$this->id) return true;
		if($user->isDev()) return true;
		return $user->isAdmin();
	}

	
	public function canEditBy($user)
	{
		if($user->id==$this->id) return true;

		if($this->isAdmin()){			
			return	$this->admin->canEditBy($user);
		}
		if($this->isTeacher()){			
			return	$this->teacher->canEditBy($user);		
		}
		if($this->isVolunteer()){
			return	$this->volunteer->canEditBy($user);			
		}

		return $user->isAdmin();

			
          
	}
	public function canDeleteBy($user)
	{
		if(!$this->canEditBy($user)) return false;

		if(count($this->roles)){
			return false;
		}
		return true;
	}

	public function defaultRole()
	{
		if(!$this->roles()->count()) return null;
		return $this->roles()->orderBy('importance','desc')->first();
		
	}

	public function addRole($roleName)
    {
        if(!$this->hasRole($roleName)){
            $role=Role::where('name',$roleName)->first();
            $this->attachRole($role);       
        } 
    }

	public function removeRole($roleName)
	{		
		if($this->hasRole($roleName)){
            $role=Role::where('name',$roleName)->first();
            $this->detachRole($role);       
        } 
		
	}
	public function toOption()
	{		
		 return [ 'text' =>   $this->profile->fullname,
                  'value' => $this->id , 
                ];
		
	}

	public function getUserId()
	{
		return $this->id;
	}

	

	

	
	
}
