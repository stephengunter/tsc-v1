<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\SupportHelper;

class Bill extends Model
{
    protected $fillable =  [ 'code', 'signup_ids',
        'amount', 'points' ,   'discount' ,
        'discount_id', 'pay_way', 'identity' ,
        'status' ,  'removed' , 'updated_by'
    ];

    public function signups() 
	{
		return $this->hasMany('App\Signup');
    }

    public function tuitions() 
	{
		return $this->hasMany('App\Tuition');
    }

    public function refund() 
	{
		return $this->hasOne('App\Refund');
    }


    public static function init()
    {
        return [            
            'amount' => '',
            'discount_id' => '',
            'discount' => '',
            'points' => '',

            'pay_way' => '',
            'status' => 0,

            'signup_ids'=>'',

            'total' => 0
            
        ];
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

    public function updateStatus()
    {
        // foreach($this->signups as $signup){
        //     $signup->points=$this->points;
        //     $signup->discount=$this->discount;
        //     $signup->discount_id=$this->discount_id;
        // }

        $signup_ids=$this->signups->pluck('id')->toArray();
        $this->signup_ids=Helper::strFromArray($signup_ids);



    }
}
