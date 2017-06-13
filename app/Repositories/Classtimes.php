<?php

namespace App\Repositories;

use App\ClassTime;

class Classtimes 
{
     
    public function getByCourse($course_id)
    {
         $classTimes=ClassTime::with('weekday')->where('course_id',$course_id)->get();
         $classTimes = $classTimes->sortBy('weekday.val');

         return $classTimes->values()->all();
    }
    
    
   

  
  
   
   
    
}