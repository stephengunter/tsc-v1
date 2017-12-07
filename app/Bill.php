<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Support\Helper;

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

    public function discount() 
	{
		return $this->hasOne('App\Discount');
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
        return   $this->tuitions()->sum('amount');
    }
    public function statusText()
    {
        $text='待繳費';
        if($this->isPayOff()) $text='已繳費';

        return $text;
    }
    public function populateViewData()
    {
        $this->invoiceMoney=$this->invoiceMoney();
        $this->statusText=$this->statusText();
        
        

        $this->canRemove=$this->canRemove();

    }

    public function isPayOff()
    {
        return $this->status == 1;
    }

    public function updateStatus()
    {
        $totalIncome=$this->invoiceMoney();

        if($totalIncome>=$this->amount)$this->status=1;
        else  $this->status=0;

        $signup_ids=$this->signups->pluck('id')->toArray();
        $this->signup_ids=Helper::strFromArray($signup_ids);

        $this->save();

        foreach($this->signups as $signup){
            $signup->updateStatus();
        } 

    }
}
