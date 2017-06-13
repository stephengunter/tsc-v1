<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Support\FilterPaginateOrder;
use Carbon\Carbon;

class Tuition extends Model
{
    use FilterPaginateOrder;

    protected $fillable = ['signup_id', 'date', 'amount', 
                        'pay_by', 'updated_by', 'refund',
                        'bank_branch', 'account_owner','account_number',
                        ];

     protected $filter =  ['date'];

                                
    public function signup()
    {
		   return $this->belongsTo('App\Signup');
	}

    public static function initialize($signup,$refund=null)
    {
        $amount=$signup->tuition;
        $isRefund=0;
        if($refund){
            $amount=$refund->getTotal();
            $isRefund=1;
        }
        return [            
            
            'signup_id' => $signup->id,
            'date' => Carbon::now()->toDateString(),
            'refund' => $isRefund,
            'amount' => $amount,
            'pay_by' => 0,
             'bank_branch'=>'',
             'account_owner'=>'',
            'account_number'=>'',
        ];
    }  

    public function canViewBy($user)
    {
        return $this->signup->canViewBy($user);
    }

    public function canEditBy($user)
	{
        return $this->signup->canEditBy($user);
          
  	}
    public function canDeleteBy($user)
	{
		return $this->canEditBy($user);
	}

    
}
