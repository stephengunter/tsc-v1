<?php

namespace App\Repositories;

use App\Weekday;

class Weekdays 
{
    public function options()
    {
        $weekdayList=Weekday::orderBy('val')->get();
        $options=[];
        foreach($weekdayList as $weekday)
        {
            $item=[ 'text' => $weekday->text , 
                     'value' => $weekday->id , 
                 ];
            array_push($options,  $item);
        }

        return $options;
       
    }
   
   
    
}