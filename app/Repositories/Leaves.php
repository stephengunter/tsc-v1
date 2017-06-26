<?php

namespace App\Repositories;

use App\Leave;
use App\LeaveType;

class Leaves 
{
    
    
    public function options()
    {
        $options=[];
        $item=[ 'text' => '缺席', 
                'value' => 0 , 
              ];
        array_push($options,  $item);

        $typeList=LeaveType::where('active',true)->get();
        foreach($typeList as $type)
        {
            $item=[ 'text' => $type->name , 
                     'value' => $type->id , 
                 ];
            array_push($options,  $item);
        }

        return $options;
    }
   
    
}