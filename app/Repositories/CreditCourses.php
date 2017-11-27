<?php

namespace App\Repositories;


use App\CreditCourse;
use App\Status;
use App\Category;
use App\Center;
use App\Teacher;
use App\Profile;
use App\Schedule;
use Carbon\Carbon;

use App\Support\Helper;
use DB;
use Excel;

class CreditCourses 
{
   public function getAll()
   {
        return CreditCourse::where('removed',false)->where('credit',true);
   }
   public function getByType($type_id)
   {
        return $this->getAll()->where('type_id',$type_id);
   }

   public function findById($id)
   {
        return $this->getAll()->where('id',$id)->first();
   }
   public function findByNumber($number)
   {
         return  $this->getAll()->where('number',$number)->first();

   }
}