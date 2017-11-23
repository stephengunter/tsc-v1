<?php

namespace App\Repositories;

use App\Discount;
use App\Course;

use Exception;

class Discounts 
{
    
    public function getAll()
    {
       return Discount::where('removed',false);
         
    }
    public function activeDiscounts($center_id)
    {
       return $this->getAll()->where('active',true)
                             ->where('center_id',$center_id);
         
    }

    public function getValidDiscounts(Course $course,$date=null)
    {
        //該中心折扣
        $activeDiscounts=$this->activeDiscounts($course->center_id);
        //今天是哪個階段
        $isStageOne=Discount::isStageOne($course->term,$date);
       
        $validDiscounts=[];
        //過濾0折扣
        if($isStageOne){
            $validDiscounts= $activeDiscounts->where('points_one', '>' , 0)
                                             ->orderBy('order','desc')->get();
            foreach($validDiscounts as $discount){
                $discount->points=$discount->points_one;
            }
        }else{
            $validDiscounts= $activeDiscounts->where('points_two' , '>' , 0)
                                             ->orderBy('order','desc')->get();
            foreach($validDiscounts as $discount){
                $discount->points=$discount->points_two;
            }
        }

        if($course->discount)    return $validDiscounts;

        //不限課程的優惠

        $filtered = $validDiscounts->filter(function ($discount) {
            return $discount->all_courses;
        });
        
        return $filtered->all();
        
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
                     'selected' => false 
                 ];
            array_push($options,  $item);
        }
        
        return $options;
    }

    public function countTuition(Course $course,$discount_id)
    {
        
        $activeDiscountIds=$this->getValidDiscounts($course);
        
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