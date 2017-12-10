<?php

namespace App;

use App\BaseCourse;
use App\Support\Validator\Course as CourseValidator;
use App\Support\Formatter\Course as CourseFormatter;
use App\Support\FilterPaginateOrder;



use App\User;
use App\Category;
use App\Photo;
use App\Volunteer;

use Carbon\Carbon;


use App\Support\Helper;

class Course extends BaseCourse
{
    use FilterPaginateOrder;
    use CourseValidator;
    use CourseFormatter;

   
    protected $fillable = [ 
        'term_id', 'center_id', 'name', 'level', 
        'number', 'caution', 'limit','min',
        'begin_date' ,  'end_date' , 'weeks', 'hours',
        'description','target',
        'tuition', 'cost' , 'materials','discount',

        'net_signup' , 'open_date' , 'close_date',
        
        'reviewed', 'active',       
        'removed' , 'updated_by'  
                            
    ];
    public  function getFillables()
    {
        return $this->fillable;
    }
    public function teachers()
    {
        return $this->belongsToMany('App\Teacher','course_teacher','course_id','teacher_id');
    }
	
    public function volunteers()
    {
        return $this->belongsToMany('App\Volunteer','course_volunteer','course_id','volunteer_id');
    }

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
    //是否停止開課
    public function classStopped()
    {
        return $this->status->classStopped();
    }
    public function canInitLessons()
    {
      
        if(  count($this->validLessons()) ) return false;
        return true;
    }



    //Getters
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
    public function getClasstimes()
    {
        return $this->classTimes()->orderBy('weekday_id')
                                         ->orderBy('on')->get();
    }
    
    public function sortClassTimes()
    {
         $this->classTimes= $this->classTimes->sortBy('weekday_id')
                                             ->sortBy('on')->values()->all();
    }
    public function validSignups()
    {
        //有效的報名
        return $this->signups->filter(function ($item) {
            return $item->isValid();
        });

    }
    public function confirmedSignups()
    {
        return $this->signups->filter(function ($item) {
            return $item->isConfirmed();
        });
    }
    public function validLessons()
    {
        return $this->lessons()->where('removed',false)->get();
    }

    public function getTuition()
    {
        return $this->tuition;
    }

    public function getCost()
    {
        return $this->cost;
    }


    //Voids
    public function generateNumber()
    {
        $term = $this->term;
        $center = $this->center;
        $category = $this->defaultCategory();
        
        if(!$category) return '';
        
        return $term->number . $center->code . $category->code . '-';
        
    }
    public function toOption()
    {
        $item=[ 
                 'text' => $this->nameWithNumber() , 
                  'value' => $this->id , 
             ];

         return $item;
    }
    public function addVolunteer(Volunteer $volunteer)
    {
        if(!$this->volunteers->contains($volunteer)) 
        {
            $this->volunteers()->attach($volunteer);
        }
           
    }

    public function updateStatus()
    {
        $this->status->updateStatus();
    }
    
    public function countTuition()
    {
        $credit_count=(int)$this->credit_count;  //學分數
        if(!$credit_count) return;

        $credit_price=$this->credit_price;
        if(!$credit_price) return;

        $this->tuition=$credit_count * $credit_price;
        
    }
    

    
    
    
    

}
