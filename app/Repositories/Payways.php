<?php

namespace App\Repositories;


class Payways 
{
    public function getAll()
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
    
    public function textPayBy($pay_id)
    {
        $payways=$this->getAll();

        return collect($payways)->first(function ($item) use($pay_id) {
            return $item['value'] ==$pay_id ;
        });
        
    }

    
   
    
     
   
    
}