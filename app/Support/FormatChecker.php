<?php

namespace App\Support;

use Carbon\Carbon;
use Exception;

class FormatChecker 
{
   public static function getCarbonDate($val)
   {
      if(!$val) return null;

      try {
         $date = Carbon::parse($val);
         return $date;
      }
      catch (Exception $err) {
         return null;
      }
   }
}