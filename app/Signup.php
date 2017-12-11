<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Support\Validator\Signup as SignupValidator;
use App\Support\Formatter\Signup as SignupFormatter;
use App\Support\FilterPaginateOrder;

use App\Course;
use App\Student;
use Carbon\Carbon;



class Signup extends Model
{
    use FilterPaginateOrder;
    use SignupValidator;
    use SignupFormatter;

   
    protected $fillable =  ['course_id',  'user_id', 'date', 'parent', 
                            'bill_id','tuition','cost' , 'net_signup',
                             'status' ,'ps',  'removed' , 'updated_by'
                        	];

    protected $filter =  ['date','status','tuition'];

    public static function initialize($user_id=0,Course $course=null)
    {
        $values= [            
            'date' => Carbon::now()->toDateString(),
            'user_id' => $user_id,
            'course_id' => 0,
            'parent' => 0,
           
            'tuition' => 0,
            'cost'=>0,
            'status' => 0,
            'ps' => '',
            'net_signup' => 1,
            'sub_courses' => []
            
        ];

        if($course){
            $values['course_id'] =$course->id;
            $values['tuition'] =$course->tuition;
        }

        return $values;
    }                         

    public function course()
    {
		   return $this->belongsTo('App\Course');
	}
    public function user() 
    {
	  	  return $this->belongsTo('App\User');
    }
    public function bill()
    {
		   return $this->belongsTo('App\Bill');
	}  
    

    

    public function hasRefund() 
	{
        $refund=$this->getRefund();
        if($refund) return true;

        return false;
    }
    
    public function getRefund() 
	{
		if(!$this->refund) return null;
        if($this->refund->removed) return null;
        return $this->refund;
    }
    
   

    
    
    public function refundRecords()
    {
        return $this->tuitions()->where('refund',true);
    }

   

    public function cancel()
    {
        //取消, 退費 , 逾期
        $this->status=-1;
        $this->save();
    }

    

    public function getAmount()
    {
        $points=0;
        if($this->bill){
            $points=$this->bill->points;
        }

        if($points) return round($this->tuition * $points);
        
        
        return $this->tuition;
    }

    

    public function updateStatus()
    {

        if($this->hasRefund()){
            $this->status = -1; //'已取消'
        }else{
           
           if($this->bill->isPayOff()){
                $this->status = 1;  //已繳費
                $this->createStudent();
           } 
           else $this->status = 0;  //待繳費
        }
        $this->save();
        
    }    

    public function createStudent($join_date='')
    {
        
        $student=$this->getStudent();
        if($student) return;

        if(!$join_date) $join_date=$this->course->begin_date;
        $values=[
           'course_id' => $this->course_id,
           'user_id' => $this->user_id,
           'join_date' => $join_date
        ];

       
  
        Student::create($values);


    }

    public function getStudent()
    {
        return Student::where('course_id',$this->course_id)
                       ->where('user_id',$this->user_id)
                       ->first();
    }


    

    public function subSignups()
    {
        return static::where('removed',false)
                     ->where('parent',$this->id);
                    
    }
    public function subSignupCourseIds()
    {
        return $this->subSignups()->pluck('course_id')->toarray();
    }
    public function subSignupCourses()
    {
        $ids= $this->subSignups()->pluck('course_id')->toarray();
        if(count($ids)){
            return Course::whereIn('id',$ids)->get();
        }else{
            return [];
        }
        
    }

    

    
}
