<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $fillable =  [ 'code',
        'amount', 'points' ,   'discount' ,
        'discount_id', 'pay_way', 'identity' ,
        'status' ,  'removed' , 'updated_by'
    ];

    public function signups() 
	{
		return $this->hasMany('App\Signup');
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
            
        ];
    }
}
