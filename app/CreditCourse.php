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
}
