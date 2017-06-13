<?php

namespace App\Repositories;

use App\Title;

class Titles 
{
    public function options($withDefault=true)
    {
        $options=[];

        if($withDefault){
             $item=[ 'text' => '' , 
                     'value' => '' , 
                 ];
            array_push($options,  $item);
        }
      
        $titleList=Title::all();
       
        foreach($titleList as $title)
        {
            $item=[ 'text' => $title->name , 
                     'value' => $title->id , 
                 ];
            array_push($options,  $item);
        }

          return $options;
       
    }

   
   
   

  
  
   
   
    
}