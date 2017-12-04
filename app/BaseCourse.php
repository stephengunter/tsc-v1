<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Category;
use App\Photo;
use App\Support\FilterPaginateOrder;
use Carbon\Carbon;


use App\Support\Helper;

class BaseCourse extends Model
{
    protected $table = 'courses';

    use FilterPaginateOrder;
    
    // protected $fillable = [ 'term_id', 'center_id', 'name', 'level', 'discount',
    // 'group','parent','must','number', 'caution',
    // 'credit_count' , 'credit_price' ,'net_signup' , 
    // 'begin_date' ,  'end_date' , 'weeks', 'hours',
    // 'tuition', 'cost' , 'materials',
    // 'description','target',
    // 'open_date' , 'close_date', 'limit','min',
    // 'display_order' , 'reviewed', 'active'	,       
    // 'removed' , 'updated_by'  
    
    // ];
	
    protected $filter =  [ 'name',  'weeks', 'hours', 'number', 'group' ,
                           'net_signup' , 'credit_count' ,'credit_price' ,
                           'begin_date' ,   'tuition' , 'cost' , 
                           'open_date' ,  'limit' , 'active',	
                         ];

    // public static function initialize($center_id=0, Course $parent_course=null)
    // {   
    //     $group=0;
    //     $parent=0;
    //     $term_id=0;
    //     $begin_date=Carbon::now()->toDateString();
    //     $end_date=Carbon::now()->addMonths(2)->toDateString();
    //     $weeks=6;
    //     $net_signup=1;
        
    //     if($parent_course){
    //         $group = 1;
    //         $parent=$parent_course->id;
    //         $term_id = $parent_course->term_id;
    //         $center_id = $parent_course->center_id;
    //         $begin_date= $parent_course->begin_date;
    //         $end_date = $parent_course->end_date;
    //         $net_signup = $parent_course->net_signup;
    //     }
    //     return [            
    //         'name' => '',
    //         'discount' => 1,
    //         'group' => $group ,
    //         'center_id' => $center_id,
    //         'term_id' => $term_id,
    //         'parent' => $parent,
    //         'must' => 0,
    //         'weeks' => $weeks,
    //         'hours' => null,
    //         'begin_date'=> $begin_date,
    //         'end_date'=> $end_date,
    //         'photo_id'=> null,
    //         'credit_count'=> 0 ,
    //         'credit_price' => null,
           
    //         'net_signup'=> $net_signup ,
    //         'active'=> 1 ,

    //         'ps' => '',
            
    //         'categories'=> [],
    //         'teachers'=> [],

    //         'isCredit'=> 0
           
    //     ];
    // }

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
    public function photo($default=true)
	{
        if($this->photo_id) return Photo::find($this->photo_id);

        if(!$default) return null;		
           
		return Photo::defaultCourse();
	}
    public function isGroup()
    {
        return $this->group;
    }
    public function isCredit()
    {
        $this->isCredit=false;
        if($this->credit_count)  $this->isCredit=(int)$this->isCredit > 0;
        return $this->isCredit;
    }

    public function isValid()
    {
        if($this->removed) return false;
        if(!$this->active) return false;
        return true;

    }

    public function openDate()
    {
        if($this->open_date) return Carbon::parse($this->open_date);

        return Carbon::parse($this->term->open_date);

    }
    public function closeDate()
    {
        if($this->close_date) return Carbon::parse($this->close_date);

        if(!$this->begin_date) return null;

        $close_before_days=config('app.course.close_before_days');

        return Carbon::parse($this->begin_date)->subDays($close_before_days);

    }

    public function getPS()
    {
        if(!$this->status) return '';
        $this->ps=$this->status->ps;
        return $this->ps;

    }
    public function defaultAmount()
    {
        
        return $this->tuition + $this->cost;

    }

    public function copyParentCourseValues($parent_id)
    {
        $parent_course=static::findOrFail($parent_id);
        $this->group=true;
        $this->parent=$parent_id;

        $this->term_id=$parent_course->term_id;
        $this->center_id = $parent_course->center_id;

        return $parent_course;
        
    }

    public function populateViewData($editNumber=false,$photo=false){
        $withNumber=false;
        $this->fullName($withNumber);

        // if($this->groupAndParent()){
        //     $this->weeks=null;
        //     $this->hours=null;

        //     $this->tuition=$this->getTuition();
        //     $this->cost=$this->getCost();
        //     $this->credit_count=$this->getCreditCounts();
        // }

        //$this->getParentCourse();

        $this->sortClassTimes();
        foreach ($this->classTimes as $classTime) {
            $classTime->weekday;
        }
        foreach ($this->teachers as $teacher) {
            $teacher->name=$teacher->getName();
        }

        if($editNumber){
            $this->numberError='';
            if($this->number){
                $parts=explode('-', $this->number);
                $this->default_number=$parts[0] . '-';
                $this->custom_number=$parts[1];
            }else{
                $this->default_number=$this->generateNumber();
                $this->custom_number='';
            }

        }

        if($photo) $this->photo= $this->photo();

        
    }
    public function countTuition()
    {
        $credit_count=(int)$this->credit_count;  //學分數
        if(!$credit_count) return;

        $credit_price=$this->credit_price;
        if(!$credit_price) return;

        $this->tuition=$credit_count * $credit_price;
        
    }

    public function validLessons()
    {
        return $this->lessons()->where('removed',false)->get();
    }
    public function canInitLessons()
    {
        if( $this->groupAndParent() ) return false;
        if(  count($this->validLessons()) ) return false;
        return true;
    }

    public function updateStatus()
    {
        $this->status->updateStatus();
    }

    public function attachCategory($category_id)
    {
        if(!$this->categories->contains($category_id)) 
        {
            $this->categories()->attach($category_id);
        }
           
    }

    public function detachCategory($category_id)
    {
        if($this->categories->contains($category_id)) 
        {
            $this->categories()->detach($category_id);
        }
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
    public function defaultCategory()
    {
        $privateCategories=$this->privateCategories();
        if($privateCategories){
            return $privateCategories[0];
        }else{
            return null;
        }
    }
    public function validSignups()
    {
        return $this->signups()->where('removed',false)
                                ->where('status' , '>','-1' );
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
        if($user->isDev()) return true;
        
        if($user->isAdmin()){
            return $user->admin->canAdminCenter($this->center);           
        } 

        if($user->isTeacher()){
            return $this->teachers->contains($user->teacher);
        }
		
		return false;
          
    } 
    public function canReviewBy($user)
	{
        if($user->isDev()) return true;
        if(!$user->isOwner()) return false;

        return $user->admin->canAdminCenter($this->center);
	}
	public function canDeleteBy($user)
	{
        if(count($this->validLessons())) return false;
		return $this->canEditBy($user);
        
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
        
        $validSignups=$this->validSignups()->get();
       
        if(!count($validSignups)) return false;


        $filtered = $validSignups->filter(function ($signup) use($user_id) {
            return $signup->user_id==$user_id;
        })->all();
        
      

        if(count($filtered)) return true;
            
        return false;
    }
    public function fullName($withNumber=true)
    {
        $fullname=$this->name;
        if($this->level) $fullname .= ' - ' . $this->level;
        
        
        if($withNumber) $fullname=$this->number . ' ' . $fullname;

        $this->fullname=$fullname;
        return $this->fullname;
    }
    public function sortClassTimes()
    {
         $this->classTimes= $this->classTimes->sortBy('weekday_id')
                                             ->sortBy('on')->values()->all();
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
    public function getParentCourse()
    {
        $parent_id=(int)$this->parent;
        $this->parentCourse=static::find($parent_id);
       

        return $this->parentCourse;
    }

    public function subCourses()
    {
        if(!$this->groupAndParent()) return null;

        return static::where('removed',false)->where('parent',$this->id)->get();
    }

    
    public function attachGroupCategory()
    {
        $groupCategory=Category::groupCategory();
        if($groupCategory) $this->attachCategory($groupCategory->id);
        
    }
    public function groupAndParent()
    {
         if(!$this->isGroup()) return false;
         return (int)$this->parent < 1;
    }

    public function generateNumber()
    {
        $term = $this->term;
        $center = $this->center;
        $category = $this->defaultCategory();
        
        if(!$category) return '';
        
        return $term->number . $center->code . $category->code . '-';
        
    }
    public function getTuition()
    {
        if($this->groupAndParent())
        {
            $subCourses=$this->subCourses();
            if(!$subCourses) return 0;
            return $subCourses->sum('tuition');
        }

        return $this->tuition;
    }
    public function getCreditCounts()
    {
        if($this->groupAndParent())
        {
            $subCourses=$this->subCourses();
            if(!$subCourses) return 0;
            return $subCourses->sum('credit_count');
        }

        return $this->credit_count;
    }
    public function getCost()
    {
        if($this->groupAndParent())
        {
            $subCourses=$this->subCourses();
            if(!$subCourses) return 0;
            return $subCourses->sum('cost');
        }

        return $this->cost;
    }

}
