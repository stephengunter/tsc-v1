<?php

namespace App\Repositories;


class Payways 
{
    public function getAll()
    {
        
        $allPayways = [
			[
				  'value' => 0,
                  'text' => '信用卡'
			],
			[
				'value' => 1,
                'text' => '銀行匯款'
			],
			[
				  'value' => 2,
                   'text' => '現金收付'
			]
        ];

        return $allPayways;
    }

    public function payByBank($pay_id)
    {
        return (intval($pay_id)==1);
    }
    public function textPayBy($pay_id)
    {
        $value=intval($pay_id);
        if($value==0) return '信用卡';
        if($value==1) return '銀行匯款';
        if($value==2) return '現金收付';
    }
   
    
     
   
    
}