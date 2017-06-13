<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Support\FilterPaginateOrder;
use App\Signup;
use App\Tuition;
use Carbon\Carbon;

class Refund extends Model
{
    public $statusList=[
        '-1' => '待審核',
        '0' => '審核中',
        '1' => '已完成'
    ];
    

     use FilterPaginateOrder;
     protected $filter =  ['date'];

     protected $primaryKey = 'signup_id';

     protected $fillable =  ['signup_id',  'date',  'number',
                             'courses_total' ,'courses_done' ,  'points' ,
                             'tuition' , 'cost', 'charge',
                              'pay_by', 'updated_by','status', 'removed',
                             'bank_branch', 'account_owner','account_number',
                            ];

    public static function initialize(Signup $signup)
    {
        return [            
            
            'signup_id' => $signup->id,
            'date' => Carbon::now()->toDateString(),
            'courses_done' => 0,
            'courses_total' => $signup->course->hours,
            'points' => 100,
            'tuition' => 0,
            'cost' => 0,
            'charge' => 0,
            'status' => -1,
             'pay_by' => 0,
             'bank_branch'=>'',
             'account_owner'=>'',
             'account_number'=>'',
            ];
    } 

    public function signup() {
		return $this->belongsTo('App\Signup');
	}

    public function hasTuitions()
    {
        if($this->signup->tuitions()->where('refund',true)->count()){
            return true;
        }else{
             return false; 
        }

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
        return $this->signup->canEditBy($user);
          
  	}
    public function canDeleteBy($user)
	{
        if($this->hasTuitions()){
            return false;
        }
		return $this->canEditBy($user);
	}

    public function getTotal()    
    {
        $cost=0;
        if($this->cost) $cost=$this->cost;

        $charge=0;
        if($this->charge) $charge=$this->charge;
        return $this->tuition + $cost - $charge;
    }
    
    public function updateStatus()
    {
         $total = $this->signup->tuitions()->where('refund',true)->sum('amount');
         if($total>=$this->getTotal()){
            $this->status=1;
         }else{
            $this->status=0;
         }
        
         $this->save();
    }    

}
