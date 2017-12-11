<?php
namespace App\Services\Signup;

use App\Term;
use App\Course;
use App\Discount;
use App\Identity;
use App\Repositories\Discounts;

use Carbon\Carbon;

class DiscountGetter
{
   public function __construct(Discounts $discounts)
   {
      $this->discounts=$discounts;
   }
   

   public function getDiscountOptions(int $center_id, int $course_count, Term $term=null, $date=null)
   {
      $activeDiscounts=$this->activeDiscounts($center_id);
      
      if(!$term)  $term=Term::defaultTerm();
      if(!$date)$date=Carbon::today();
      //今天是哪個階段
      $isStageOne=Discount::isStageOne($term,$date);

      //過濾0折扣
      $validDiscounts=$this->filterDiscount($activeDiscounts , $isStageOne ,$course_count);
      $options=$this->optionsConverting($validDiscounts);
      

     
      return $options;
       
   }

   private function optionsConverting($discounts)
   {
      $options=[];
      foreach($discounts as $discount){
         if(count($discount->identities)){
            foreach($discount->identities as $identitiy){
               $item=array(
                  'text' => $identitiy->name , 
                 
                  'identity_id' => $identitiy->id,
   
                  'value' => $discount->id ,
                  'points' => $discount->points ,
                  'bird' => $discount->isBird() 
               );

               if($discount->isBird()){
                  $item['text'] .= ' - ' . config('course.bird_discount_name');
               }

               $item['text'] .= '  '. $discount->getFormattedPoints($discount->points);
               
              
               array_push($options,  $item);
            }
            
            
         }else{
            $item= array(
               'text' => $discount->getNameWithDiscount() , 
   
               'identity_id' => 0,
   
   
               'value' => $discount->id ,
               'points' => $discount->points ,
               'bird' => $discount->isBird() 
           
            );

            array_push($options,  $item);
         }
      }
     
      return $options;

     
       
   }

   

   private function activeDiscounts($center_id)
   {
      return $this->discounts->activeDiscounts($center_id);
   }

   private function filterDiscount($activeDiscounts , $isStageOne,int $course_count)
   {
      $activeDiscounts=$activeDiscounts->filter(function ($item) use($course_count) {
         return $item->course_count <= $course_count ;
      });
       
     
      $validDiscounts=[];
      //過濾0折扣
      if($isStageOne){
         $validDiscounts = $activeDiscounts->filter(function ($item) {
            return $item->points_one > 0 ;
         });

         
         foreach($validDiscounts as $discount){
            $discount->populateViewData($isStageOne);
         }
      }else{
         $validDiscounts = $activeDiscounts->filter(function ($item) {
            return $item->points_two > 0 ;
         });
         
         foreach($validDiscounts as $discount){
            $discount->populateViewData($isStageOne);
         }
      }

      return $validDiscounts;
   }

   
}