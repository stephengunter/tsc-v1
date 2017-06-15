<?php

namespace App\Repositories;

use App\ClassTime;

class Classtimes 
{
     
    public function getByCourse($course_id)
    {
        return ClassTime::with('weekday')->where('course_id',$course_id);
    }
    
    
   

  
  
   
   
    
}