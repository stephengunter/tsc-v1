<?php

namespace App\Repositories;

use App\Tuition;
use App\Support\Helper;

class Tuitions 
{
    public function getAll()
    {
         return Tuition::where('refund',false);
    }
   
    public function findOrFail($id)
    {
        $record= $this->getAll()->where('id',$id)->first();
        if(!$record)  abort(404);
       
        return $record;
       
    }
    public function getBySignup($signup_id)
    {
        return  $this->getAll()->where('signup_id', $signup_id);
           
    }
   
    
    
}