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

    public $signupStatusList=array(
        [
			'value' => -3,
			'text' => '已額滿'
		],
        [
			'value' => -2,
			'text' => '已截止'
		],
        [
			'value' => -1,
			'text' => '尚未開始'
		],
		[
			'value' => 0,
			'text' => '已停止'
		],
		[
			'value' => 1,
			'text' => '進行中'
        ],
        
    );
    public $classStatusList=array(
        [
			'value' => -1,
			'text' => '尚未開始'
		],
		[
			'value' => 0,
			'text' => '已停止'
		],
		[
			'value' => 1,
			'text' => '進行中'
        ],
        [
			'value' => 2,
			'text' => '已結束'
        ],
        
    );
    public $registerStatusList=[  
        [
			'value' => 0,
			'text' => '未完成'
		],
		[
			'value' => 1,
			'text' => '已完成'
		],     
       
    ];
   

    public function course() 
    {
		return $this->belongsTo('App\Course');
    }
    public function  canSignup($is_netSignup=true)
    {
        $signupStatus= (int)$this->signup;
        if($is_netSignup){
           
            return $signupStatus >= 1;
        }

        if($signupStatus==-3) return false;  //已額滿

        if($signupStatus==0) return false;   //已停止

        return true;
        
    }
    public static function initialize($course)
    {
        $class=static::getClassStatus($course);
        $signup=static::getSignupStatus($course);

        if(!$course->avtive) $signup=0; //已停止開課
        
        
        return [   
            'ps' => '',         
            'signup' => $signup,
            'register' => 0,
            'class' => $class
           
        ];
    }

    public function updateStatus()
    {
        $course=$this->course;

        if($course->active){
            $this->class=static::getClassStatus($course);
            $this->signup=static::getSignupStatus($course);
          
        }else{
            $this->class=0;  //已停止
            $this->signup=0;  //已停止
        }
        
        $this->save();

       

    }

    
    
    public static function getClassStatus($course) 
    {

        if(!$course->active) return 0; //停止開課

        $today=Carbon::today();
        $begin_date=$course->begin_date;
        $end_date=$course->end_date;

        
        $begin = Carbon::parse($begin_date);
        $end = Carbon::parse($end_date);

       

        if($today->lt($begin)) return -1;  //尚未開始
        if($today->gt($end)) return 2;   //已結束
        return 1;    //進行中

      
	}

    public static function getSignupStatus($course) 
    {

        $limit=(int)$course->limit;
        
        if(count($course->confirmedSignups())>= $limit)
        {
            return  -3; //已額滿
        }


        $today=Carbon::today();
        $openDate=$course->openDate();
        
        $closeDate=$course->closeDate();

        if($today->lt($openDate)) return -1;  // 尚未開始
        if($today->gt($closeDate)) return -2;  //已截止
        return 1;  //進行中
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
        
		return $this->course->canReviewBy($user);
          
	} 

}
