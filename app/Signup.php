<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Support\FilterPaginateOrder;
use App\Course;
use Carbon\Carbon;

class Signup extends Model
{
    public $statusList=[
        '-1' => '已取消',
        '0' => '待繳費',
        '1' => '已繳費'
    ];

    use FilterPaginateOrder;
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

    
    
    
    public function isValid()
    {
          //是否被有效
        if($this->removed) return false; 

        return (int)$this->status >= 0;
       
    }

    public function isConfirmed()
    {
        //是否被取消
        if(!$this->isValid()) return false; 

        return (int)$this->status > 0;
    }

    public function hasRefund() 
	{
		if(!$this->refund) return null;
        if($this->refund->removed) return null;
        return $this->refund;
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

    public function canViewBy($user)
	{
        if($user->isDev()) return true;
		if($user->id==$this->user_id) return true;
        if($user->isAdmin()){
           return true;
        }

        return false; 
          
  	}
    public function canEditBy($user)
	{
		if($user->id==$this->user_id) return true;
        return $this->course->canEditBy($user);
          
    }
    public function canRemove()
    {
        //可刪除
        if($this->status==1)  return false;
        //有繳費紀錄
        if($this->tuitions()->count()) return false;

        return true;
    }
    public function canRemoveBy($user)
	{
        if(!$this->canRemove()) return false;

        return $this->canEditBy($user);
	}
    public function canDeleteBy($user)
	{
		return $this->canRemoveBy($user);
	}
    public function canCancelBy($user)
	{
       if(!$this->canCancel()) return false;
		   return $this->canEditBy($user);
    }
    
    public function canBeginPay() 
    {
         return $this->totalIncome() == 0;
         
    }

    public function canCancel() 
    {
        $status=(int)$this->status;
        if($status > 0){
        return false;
        }else{
        return true;
        }
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

    
    public function canPay()
    {
        if($this->status==0){
            //待繳費
            if($this->course->canceled()) return false;

            //是否額滿
            if($this->course->peopleFulled()) return false;

            return true;
        }

        return false;
    }
    public function statusText()
    {
        $text='待繳費';
        if($this->status==1) $text='已繳費';
        else if($this->status==-1) $text='已取消';


        return $text;
    }
    public function populateViewData()
    {
        $this->course->fullname();
        $this->statusText=$this->statusText();
        
        $this->amount =$this->getAmount();

        $this->canRemove=$this->canRemove();

    }
    

    public function updateStatus()
    {

        if($this->hasRefund()){
            $this->status = -1; //'已取消'
        }else{
           
           if($this->bill->isPayOff())  $this->status = 1;  //已繳費
           else $this->status = 0;  //待繳費
        }
        $this->save();

        
    }    
    public function formattedPoints()
    {
        if(!$this->points) return '';

        $strValue=(String)$this->points;

        return str_replace('0','',$strValue);
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
