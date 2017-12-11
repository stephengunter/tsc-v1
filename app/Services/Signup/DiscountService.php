<?php
namespace App\Services\Signup;

use App\Term;
use App\Course;
use App\Discount;
use App\Services\Signup\DiscountGetter;
use Carbon\Carbon;

class DiscountService
{
   public function __construct(DiscountGetter $discountGetter)
   {
      $this->discountGetter=$discountGetter;
   }
    
   public function getDiscountOptions(int $center_id, int $course_count,Term $term=null, $date=null)
   {
      
      return $this->discountGetter->getDiscountOptions($center_id, $course_count,$term, $date);
       
   }



    public function  getDiscountOptionsByCourse(Course $course)
    {
        $validDiscounts=$this->discounts->getValidDiscounts($course,$date);
        return $this->discounts->optionsConverting($validDiscounts);
        
    } 

   

    public function  autoDecideDiscount($courseCount)
    {
        $term=$this->terms->latest();
        //今天是哪個階段
        $isStageOne=Discount::isStageOne($term);
      
       
        $activeDiscounts=$this->discounts->getOnlineDiscounts($isStageOne , $courseCount);
        
        $discount=null;
        if(count($activeDiscounts)){
           //最低的折扣
           $sorted = $activeDiscounts->sortBy('points');            
           $discount= $sorted->values()->first();
        }

        if($discount){
            $discount->isStageOne=$isStageOne;
            if($isStageOne){
                $discount->name .= ' ' . $term->bird_date .  '前繳費' ;
            }
            

        } 

        return $discount;
    }

    
  
    
   

   
    public function countTuition(Course $course,$discount_id,Carbon $date=null)
    {

        $activeDiscountIds=$this->discounts->getValidDiscounts($course,$date);
        
        $discount = $activeDiscountIds->filter(function($item) use($discount_id) {
            return $item->id == $discount_id;
        })->first();

        if(!$discount) throw new Exception;

        $points=$discount->points;
        $tuition=$course->tuition*$points/100;


        $discount->tuition=round($tuition);
        

        return $discount;
    }

    
}