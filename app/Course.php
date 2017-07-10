<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Photo;
use App\Support\FilterPaginateOrder;
use Carbon\Carbon;

class Course extends Model
{
    use FilterPaginateOrder;
   
    
	protected $fillable = [ 'term_id', 'center_id', 'name', 
                            'credit_count' ,'net_signup' , 
                            'begin_date' ,  'end_date' , 'weeks', 'hours',
                            'tuition', 'cost' , 'materials',
                            'description','target',
                            'open_date' , 'close_date', 'limit','min',
                            'display_order' , 'reviewed', 'active'	,       
                            'removed' , 'updated_by'   
                            
                            ];
	
    protected $filter =  [ 'name',  'weeks', 'hours', 'number',
                           'net_signup' , 'credit_count' ,
                           'begin_date' ,   'tuition' , 'cost' , 
                           'open_date' ,  'limit' , 'active',	
                         ];

    public static function initialize($center_id)
    {
        return [            
            'name' => '',
            'center_id' => $center_id,
            'weeks' => 6,
            'hours' => null,
            'begin_date'=>Carbon::now()->toDateString(),
            'end_date'=>Carbon::now()->addMonths(2)->toDateString(),
            'photo_id'=> null,
            'credit_count'=> 0 ,
           
            'net_signup'=> 1 ,
            'active'=> 0 ,
            
            'categories'=> [],
            'teachers'=> [],
           
        ];
    }

    public function status() 
	{
		return $this->hasOne('App\Status');
	}

    public function term() {
		return $this->belongsTo('App\Term');
	}

    public function center() {
		return $this->belongsTo('App\Center');
	}

    public function admission() 
	{
		return $this->hasOne('App\Admission');
	}
    public function register() 
	{
		return $this->hasOne('App\Register');
	}

	public function categories()
    {
        return $this->belongsToMany('App\Category','course_category');
    }
    
    public function teachers()
    {
        return $this->belongsToMany('App\Teacher','course_teacher','course_id','teacher_id');
    }
    public function classTimes() 
	{
		return $this->hasMany('App\ClassTime');
	}
    public function schedules() 
	{
		return $this->hasMany('App\Schedule');
	}
    public function lessons() 
	{
		return $this->hasMany('App\Lesson');
	}
    public function signups() 
	{
		return $this->hasMany('App\Signup');
	}
    public function students() 
	{
		return $this->hasMany('App\Student');
	}

    public function classroom()
    {
          return $this->belongsTo('App\Classroom');
    }
    public function photo()
	{
		if(!$this->photo_id){
			return Photo::defaultCourse();
		}
		return Photo::find($this->photo_id);

	}

   

    public function isValid()
    {
        if($this->removed) return false;
        if(!$this->active) return false;
        return true;

    }

    public function validLessons()
    {
        return $this->lessons()->where('removed',false)->get();
    }
    public function canInitLessons()
    {
        if(  count($this->validLessons()) ) return false;
        return true;
    }

    public function validCategories()
    {
        if(!$this->categories()->count()) return null;
        return $this->categories()->where('removed',false)
                    ->where('active',true)->get();
    }
    public function privateCategories()
    {
        if(!$this->categories()->count()) return null;
        return $this->categories()->where('removed',false)
                    ->where('active',true)
                    ->where('public',false)->get();
    }
    public function validSignups()
    {
        return $this->signups()->where('removed',false);
    }
   
    public function canCreateAdmit()
    {
        if($this->admission) return false;
        if($this->classStopped()) return false;
        return true;
    }
    public function canCreateRegister()
    {
        if(!$this->admission) return false;
        if($this->register) return false;
        if($this->classStopped()) return false;
        return true;
    }
    public function getClasstimes()
    {
        return $this->classTimes()->orderBy('weekday_id')
                                         ->orderBy('on')->get();
    }
    public function classStopped()
    {
        return $this->status->classStopped();
    }

    public function canViewBy($user)
    {
       return true;
    }

    public function canEditBy($user)
	{
        if($user->isAdmin()){
           return $this->center->canEditBy($user);
        } 

        if($user->isTeacher()){
            return $this->teachers->contains($user->teacher);
        }
		
		return false;
          
	} 
	public function canDeleteBy($user)
	{
        if(count($this->validLessons())) return false;
		if($user->isAdmin()){
           return $this->center->canEditBy($user);
        } 
        
        return  false;
        
	}

    public function canSignup()
    {
        if(!$this->isValid())  return  false;
        $status=$this->signupStatus();
        if($status==1) return true;
        return false;
        
    }
    public function signupStatus()
    {
         $today=Carbon::today();
         $open = Carbon::parse($this->open_date);
         $close = Carbon::parse($this->close_date);

         if($today->lt($open)) return 0;
         if($today->gt($close)) return 2;
         return 1;

    }

    public function hasSignupBy($user_id)
    {
        if(!count($this->validSignups())) {
               return false;
        }

        if($this->validSignups()->where('user_id',$user_id)->count()){
            return true;
        }else{
            return false;
        }
    }
    public function fullName()
    {
        return $this->number . ' ' . $this->name;
    }
    public function nameWithNumber()
    {
        return $this->name . ' (' . $this->number . ')';
    }
    public function toOption()
    {
        $item=[ 
                 'text' => $this->nameWithNumber() , 
                  'value' => $this->id , 
             ];

         return $item;
    }

}
