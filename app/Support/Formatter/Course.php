<?php

namespace App\Support\Formatter;

trait Course
{
   public function fullName($withNumber=true)
   {
       $fullname=$this->name;
       if($this->level) $fullname .= ' - ' . $this->level;
       
       
       if($withNumber) $fullname=$this->number . ' ' . $fullname;

       
       return $fullname;
   }
   public function nameWithNumber()
   {
       return $this->name . ' (' . $this->number . ')';
   }
   public function populateViewData($editNumber=false,$photo=false)
   {
      $withNumber=false;
      $this->fullName=$this->fullName($withNumber);

      $this->fulled=$this->peopleFulled();

      $this->sortClassTimes();
      foreach ($this->classTimes as $classTime) {
          $classTime->weekday;
      }
      foreach ($this->teachers as $teacher) {
          $teacher->name=$teacher->getName();
      }

      if($editNumber){
          $this->numberError='';
          if($this->number){
              $parts=explode('-', $this->number);
              $this->default_number=$parts[0] . '-';
              $this->custom_number=$parts[1];
          }else{
              $this->default_number=$this->generateNumber();
              $this->custom_number='';
          }

      }

      if($photo) $this->photo= $this->photo();

      
  }
}