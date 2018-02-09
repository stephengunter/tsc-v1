<?php

namespace App\Repositories;


class Payways 
{
    public static function getAll()
    {

        return config('app.payways');
    }

    

    public function payByCreditCard($payway_id)
    {
        return (int)$payway_id == 0;
    }

    public function payBySeven($payway_id)
    {
        return (int)$payway_id == 1;
    }
    
    public static function textPayBy($pay_id)
    {
        $payways=static::getAll();

        $payway = collect($payways)->first(function ($item) use($pay_id) {
            return $item['value'] ==$pay_id ;
        });

        return $payway['text'];
        
    }

    
   
    
     
   
    
}