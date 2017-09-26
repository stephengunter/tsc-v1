<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Status extends Model
{
    protected $primaryKey = 'course_id';

    protected $fillable = [
        'class','signup','register',
	   'ps', 'updated_by'
      
	];

    public $signupStatusList=[
        '-1' => '未開始',
        '0' => '已停止',
        '1' => '進行中',
        '2' => '已截止',
    ];
    public $registerStatusList=[       
        '0' => '未完成',
        '1' => '已完成',
    ];
    public $classStatusList=[       
       '-1' => '尚未開課',
        '0' => '停止開課',
        '1' => '進行中',
        '2' => '已結束',
    ];

    public static function initialize($course)
    {
        
        $signup=static::getSignupStatus($course);
        $class=static::getClassStatus($course);
        
        return [            
            'signup' => $signup,
            'register' => 0,
            'class' => $class
           
        ];
    }

    public function updateStatus()
    {
        $course=$this->course;
        if((int)$this->signup!=0 ){
            $this->signup=static::getSignupStatus($course);
        }
        if((int)$this->class!=0 ){
            $this->class=static::getClassStatus($course);
        }else{
            //停止開課
           
            if($course->active){
                $course->active=false;
                $course->save();
            }
        }

        if(!$course->reviewed){
            if($course->active){
                $course->active=false;
                $course->save();
            }
        } 

        
        $this->save();

    }

    public function course() {
		return $this->belongsTo('App\Course');
	}

    public static function getSignupStatus($course) {
       
        $today=Carbon::today();
        $open_date=$course->open_date;
        if(!$open_date){
            if($course->getParentCourse())
            {
                $open_date=$course->parentCourse->open_date;
            }
        }
        $close_date=$course->close_date;
        if(!$close_date){
            if($course->getParentCourse())
            {
                $close_date=$course->parentCourse->close_date;
            }
        }
        $open = Carbon::parse($open_date);
        $close = Carbon::parse($close_date);

        if($today->lt($open)) return -1;
        if($today->gt($close)) return 2;
        return 1;
	}
    public static function getClassStatus($course) 
    {
        $today=Carbon::today();
        $begin_date=$course->begin_date;
        if(!$begin_date){
            if($course->getParentCourse())
            {
                $begin_date=$course->parentCourse->begin_date;
            }
        }
        $end_date=$course->end_date;
        if(!$end_date){
            if($course->getParentCourse())
            {
                $end_date=$course->parentCourse->end_date;
            }
        }


        $begin = Carbon::parse($begin_date);
        $end = Carbon::parse($end_date);

        if($today->lt($begin)) return -1;  //尚未開課
        if($today->gt($end)) return 2;   //已結束
        return 1;    //進行中

      
	}

    public function hasRemoved()
    {
        return $this->course->removed;
    }
    public function signupStopped()
    {
        $status=(int)$this->signup;
        return ($status==0);
    }
    public function classStopped()
    {
        $status=(int)$this->class;
        return ($status==0);
    }

    public function canEditBy($user)
	{
        
		return $this->course->canEditBy($user);
          
	} 
	public function canDeleteBy($user)
	{
        return $this->course->canDeleteBy($user);
        
	}

}
