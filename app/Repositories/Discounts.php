<?php

namespace App\Repositories;

use App\Discount;
use App\Course;
use App\Term;


use Carbon\Carbon;
use Exception;

class Discounts 
{
    
    public function getAll()
    {
       return Discount::where('removed',false);
         
    }
    public function globalDiscount()
    {
        return $this->getAll()->where('need_prove',false);
    }
    public function activeDiscounts($center_id=0)
    {
        $globalDiscount=$this->globalDiscount()->where('active',true)->get();

        if(!$center_id)  return $globalDiscount;

       

        $centerDiscounts= $this->getAll()->where('active',true)
                             ->where('center_id',$center_id)->get();

        return $globalDiscount->merge($centerDiscounts);
         
    }

    public function getOnlineDiscounts(bool $isStageOne , int $courseCount)
    {
        $activeDiscounts=$this->activeDiscounts();

        $activeDiscounts = $activeDiscounts->filter(function ($item) use($courseCount) {
            return $courseCount >= $item->course_count;
        });

        //過濾0折扣
        $validDiscounts=$this->filterDiscount($activeDiscounts , $isStageOne);
        

        return $validDiscounts;
    }

    private function filterDiscount($activeDiscounts , $isStageOne)
    {
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

    public function getDiscountOptions(int $center_id )
    {
        $date=Carbon::today();
        $term=Term::defaultTerm();

        $activeDiscounts=$this->activeDiscounts($center_id);
        
        //今天是哪個階段
        $isStageOne=Discount::isStageOne($term,$date);

       //過濾0折扣
        $validDiscounts=$this->filterDiscount($activeDiscounts , $isStageOne);
        

        return $this->optionsConverting($validDiscounts);
        
    }

    public function getValidDiscounts(Course $course,Carbon $date=null)
    {
        $activeDiscounts=$this->activeDiscounts($course->center_id);
        
        //今天是哪個階段
        $isStageOne=Discount::isStageOne($course->term,$date);

       //過濾0折扣
        $validDiscounts=$this->filterDiscount($activeDiscounts , $isStageOne);
        

        return $validDiscounts;

        //if($course->discount)    return $validDiscounts;

        //不限課程的優惠,針對"不優惠課程"

        // $filtered = $validDiscounts->filter(function ($discount) {
        //     return $discount->all_courses;
        // });
        
        // return $filtered->all();
        
    }
    


    public function findOrFail($id)
    {
       return Discount::findOrFail($id); 
         
    }

    public function getByKey($key)
    {
        $this->getAll()->where('key',$key)->first();
    }
    
    public function store($values)
    {
          $discount=Discount::create($values);

          return $discount;
    }
    public function update($values, $id)
    {
         $discount=Discount::findOrFail($id); 
         $discount->update($values);

          return $discount;
    }
    public function updateDisplayOrder($id,$order,$updated_by)
    {
        $discount = Discount::findOrFail($id);        

        $discount->order= (int)$order;           
        $discount->updated_by= $updated_by;

        if($order>=0){
            $discount->active=true;
        }else{
            $discount->active=false;
        }
        
        
        $discount->save();
       
        return $discount;

    }
    
    public function delete($id,$admin_id)
    {
        $discount=Discount::findOrFail($id); 

        $values=[
            'active' =>0,
            'removed' => 1,
            'updated_by' => $admin_id
        ];
        
        $discount->update($values);
        
    }

    
    public function optionsConverting($discounts)
    {
        $options=[];
        foreach($discounts as $discount)
        {
             $item=[ 'text' => $discount->getNameWithDiscount() , 
                     'value' => $discount->id ,
                     'points' => $discount->points ,
                     'bird' => $discount->isBird() 
                 ];
            array_push($options,  $item);
        }
        
        return $options;
    }

    public function countTuition(Course $course,$discount_id,$date=null)
    {

        $activeDiscountIds=$this->getValidDiscounts($course,$date);
        
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