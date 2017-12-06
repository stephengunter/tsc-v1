<?php

namespace App;

use App\BaseCourse;

use App\Category;
use App\Photo;
use App\Support\FilterPaginateOrder;
use Carbon\Carbon;


use App\Support\Helper;

class Course extends BaseCourse
{
    
    protected $fillable = [ 'term_id', 'center_id', 'name', 'level', 
                            'number', 'caution', 'limit','min',
                            'begin_date' ,  'end_date' , 'weeks', 'hours',
                            'description','target',
                            'tuition', 'cost' , 'materials','discount',

                            'net_signup' , 'open_date' , 'close_date',
                            
                            'reviewed', 'active',       
                            'removed' , 'updated_by'  
                            
                            ];
	
    

    public static function initialize($term_id,$center_id)
    {   
       
        $begin_date=Carbon::now()->toDateString();
        $end_date=Carbon::now()->addMonths(2)->toDateString();
        $weeks=6;
        $hours=12;
       
        
        return [            
            'name' => '',
            'discount' => 1,
          
            'center_id' => $center_id,
            'term_id' => $term_id,
          
            'weeks' => $weeks,
            'hours' => $hours,
            'begin_date'=> $begin_date,
            'end_date'=> $end_date,
            'photo_id'=> null,
           
           
            'net_signup'=> 1 ,
            'active'=> 1 ,
           
            
            'categories'=> [],
            'teachers'=> [],

           
           
        ];
    }

    

    //是否額滿
    public function peopleFulled()
    {
        return $this->status->peopleFulled();
    }

    //是否停止開課
    public function canceled()
    {
        return !$this->active;
    }

    public function canSignup($isNetSignup=true)
    {
        return $this->status->canSignup($isNetSignup);
        
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
        
        $validSignups=$this->validSignups();
       
        if(!count($validSignups)) return false;


        $filtered = $validSignups->filter(function ($signup) use($user_id) {
            return $signup->user_id==$user_id;
        })->all();
        
      

        if(count($filtered)) return true;
            
        return false;
    }
   

    public function populateViewData($editNumber=false,$photo=false){
        $withNumber=false;
        $this->fullName($withNumber);

        $this->fulled=$this->peopleFulled();

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
        return $this->signups->filter(function ($item) {
            return $item->isValid();
        })->all();

    }
    public function confirmedSignups()
    {
        return $this->signups->filter(function ($item) {
            return $item->isConfirmed();
        })->all();
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
