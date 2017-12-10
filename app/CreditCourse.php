<?php

namespace App;

use App\BaseCourse;

use App\Category;
use App\Photo;
use App\Support\FilterPaginateOrder;
use Carbon\Carbon;


use App\Support\Helper;

use Illuminate\Database\Eloquent\Model;

class CreditCourse extends BaseCourse
{
    protected $fillable = [ 'name', 
    'number', 'caution', 'limit','min',
    'group' , 'parent', 'type_id', 'college_id',
    'time' , 'location' , 'tel' , 'signup_charge',
    'credit_count' , 'credit_price' , 'signup_charge',
    'decision_date',
    'begin_date' ,  'end_date' , 'weeks', 'hours',
    'description','target',
    'tuition', 'cost' , 'materials','discount',

    'net_signup' , 'open_date' , 'close_date',
    
    'reviewed', 'active',       
    'removed' , 'updated_by'  
    
    ];

    public function populateViewData($editNumber=false,$photo=false){
        $withNumber=false;
        $this->fullName($withNumber);

        if($this->groupAndParent()){
            $this->weeks=null;
            $this->hours=null;

            $this->tuition=$this->getTuition();
            $this->cost=$this->getCost();
            $this->credit_count=$this->getCreditCounts();
        }

        $this->getParentCourse();

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

    // public function canCreateAdmit()
    // {
    //     if($this->admission) return false;
    //     if($this->classStopped()) return false;
    //     return true;
    // }
    // public function canCreateRegister()
    // {
    //     if(!$this->admission) return false;
    //     if($this->register) return false;
    //     if($this->classStopped()) return false;
    //     return true;
    // }
}
