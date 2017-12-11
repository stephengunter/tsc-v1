<?php

namespace App\Support\Validator;


trait Signup
{
   // statusList= array(
   //    '-1' => '已取消',
   //    '0' => '待繳費',
   //    '1' => '已繳費'
   // );
   public function isValid()
   {
         //是否被刪除
       if($this->removed) return false; 
      //是否被取消
       return (int)$this->status >= 0;
      
   }
   public function isCanceled()
   {
       return (int)$this->status < 0;
      
   }

   //已繳費的
   public function isConfirmed()
   {
       if(!$this->isValid()) return false; 

       return (int)$this->status > 0;
   }

  
   
   public function canBeginPay() 
   {
      return $this->totalIncome() == 0;
      
   }
   public function canPay()
   {
       if($this->status==0){
           //待繳費
           if($this->course->canceled()) return false;

           //是否額滿
           if($this->course->peopleFulled()) return false;

           return true;
       }

       return false;
   }
   



   public function canViewBy($user)
	{
      if($user->isDev()) return true;
		if($user->id==$this->user_id) return true;
      if($user->isAdmin()){
         return true;
      }

        return false; 
          
  	}
   public function canEditBy($user)
	{
		if($user->id==$this->user_id) return true;
        return $this->course->canEditBy($user);
          
   }
   public function canRemove()
   {
      //可刪除
      if($this->isConfirmed())  return false;
      //有繳費紀錄
      if($this->tuitions()->count()) return false;

      return true;
   }
   public function canRemoveBy($user)
	{
      if(!$this->canRemove()) return false;

      return $this->canEditBy($user);
	}
   public function canDeleteBy($user)
	{
		return $this->canRemoveBy($user);
	}
   
}