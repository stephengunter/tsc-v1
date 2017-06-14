<?php

namespace App\Repositories;

use App\Identity;

class Identities 
{
    public function options()
    {
        $options=[];
        $identityList=Identity::all();
        foreach($identityList as $identity)
        {
            $item=[ 'text' => $identity->name , 
                     'value' => $identity->id , 
                 ];
            array_push($options,  $item);
        }

        return $options;
       
    }
    
}