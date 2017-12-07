<?php

namespace App\Repositories;

use App\Student;
use App\Signup;


class Students 
{
   public function getAll()
   {
        return Student::all();
   }

   public function store(Signup $signup,$join_date='')
   {
      if(!$join_date) $join_date=$signup->course->begin_date;
      $values=[
         'course_id' => $signup->course_id,
         'user_id' => $signup->user_id,
         'join_date' => $join_date
      ];

      return Student::create($values);


      
   }
}