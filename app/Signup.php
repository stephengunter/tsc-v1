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
                              'tuition','cost' ,'points' ,   'discount' ,
                             'identity' , 'discount_id', 'net_signup',
                             'status' ,  'removed' , 'updated_by'
                        	];

    protected $filter =  ['date','status','tuition','discount'];

    public static function initialize($user_id=0,$course_id=0)
    {
        return [            
            'date' => Carbon::now()->toDateString(),
            'user_id' => $user_id,
            'course_id' => $course_id,
            'parent' => 0,
            'discount_id' => 0,
            'tuition' => 0,
            'cost'=>0,
            'status' => 0,
            'net_signup' => 1,
            'sub_courses' => []
            
        ];
    }                         

    public function course()
    {
		   return $this->belongsTo('App\Course');
	}
    public function user() 
    {
	  	  return $this->belongsTo('App\User');
    }
      

    public function tuitions() 
	{
		return $this->hasMany('App\Tuition');
	}
    public function refund() 
	{
		return $this->hasOne(Refund::class);
	}

    public function hasRefund() 
	{
		if(!$this->refund) return null;
        if($this->refund->removed) return null;
        return $this->refund;
	}

    public function invoiceMoney()
    {
        $totalIncome=$this->incomeRecords()->sum('amount');
                                        
        return $totalIncome;
    }
    public function incomeRecords()
    {
        return $this->tuitions()->where('refund',false);
    }
   


    public function canViewBy($user)
	{
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
    public function canDeleteBy($user)
	{
       if($this->tuitions()->count()) return false;
		   return $this->canEditBy($user);
	}
    public function canCancelBy($user)
	{
       if(!$this->canCancel()) return false;
		   return $this->canEditBy($user);
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
    public function updateStatus()
    {
         if($this->hasRefund()){
             $this->status = -1;
         }else{
            $total = $this->tuitions()->where('refund',false)->sum('amount');
            if($total>=$this->tuition){
                $this->status=1;
            }else{
                $this->status=0;
            }
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
