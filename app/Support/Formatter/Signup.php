<?php

namespace App\Support\Formatter;


trait Signup
{
   public function statusList()
   {
      return array(
         '-1' => '已取消',
         '0' => '待繳費',
         '1' => '已繳費'
      );
   }
   public function statusText()
   {
       $text='待繳費';
       if($this->status==1) $text='已繳費';
       else if($this->status==-1) $text='已取消';


       return $text;
   }

   public function populateViewData()
   {
       $this->course->fullName=$this->course->fullName();
       $this->statusText=$this->statusText();
       
       $this->amount =$this->getAmount();

       $this->canRemove=$this->canRemove();

   }

   public function formattedPoints()
   {
       if(!$this->points) return '';

       $strValue=(String)$this->points;

       return str_replace('0','',$strValue);
   }
}