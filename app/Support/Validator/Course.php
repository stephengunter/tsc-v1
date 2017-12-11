<?php

namespace App\Support\Validator;

use App\User;

trait Course
{
   
   public function canEditBy($user)
   {
       if($user->isDev()) return true;
       
       if($user->isAdmin()){
           return $user->admin->canAdminCenter($this->center);           
       } 

       if($user->isTeacher()){
           return $this->teachers->contains($user->teacher);
       }
     
     return false;
         
   } 
   public function canReview()
   {
      if($this->reviewed) return false;
      if(!$this->name) return false;
      if(!$this->number) return false;

      if(!$this->weeks) return false;
      if(!$this->hours) return false;
      
      if(!$this->begin_date) return false;
      if(!$this->end_date) return false;

      if(!$this->tuition) return false;
      if(!$this->limit) return false;

      if(!$this->classTimes()->count()) return false;
      if(!$this->categories()->count()) return false;
      if(!$this->teachers()->count()) return false;

      return true;
   }
   public function canReviewBy($user)
   {
      if(!$this->canReview()) return false;


      if($user->isDev()) return true;
      if(!$user->isOwner()) return false;

      return $user->admin->canAdminCenter($this->center);
   }

   public function canDeleteBy($user)
   {
      if(count($this->validLessons())) return false;
      return $this->canEditBy($user);
       
   }

   public function canSignup($isNetSignup=true)
   {
       return $this->status->canSignup($isNetSignup);
       
   }
   public function canSignupBy(User $user,$isNetSignup=true)
   {
       if(!$this->canSignup($isNetSignup)) return '此課程目前無法報名';

       if($this->hasSignupBy($user->id)) return '此學員已報名過此課程了';

       return '';
       
   }


}