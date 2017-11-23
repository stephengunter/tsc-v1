<?php

namespace App\Repositories;


class Payways 
{
    public function getAll()
    {

        return config('app.payways');
    }

    

    
    public function textPayBy($pay_id)
    {
        $payways=$this->getAll();

        return collect($payways)->first(function ($item) use($pay_id) {
            return $item['value'] ==$pay_id ;
        });
        
    }

    
   
    
     
   
    
}