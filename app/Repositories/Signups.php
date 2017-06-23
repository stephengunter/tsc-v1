<?php

namespace App\Repositories;

use App\Signup;
use App\Support\Helper;
use Carbon\Carbon;
use DB;

class Signups 
{
    public function getAll()
    {
         return Signup::where('removed',false);
    }
   
    public function findOrFail($id)
    {
        return Signup::findOrFail($id);
       
    }
   
    public function statusOptions()
    {
         $options=[];
         $success=[ 'text' => '已繳費' ,
                     'value' => 1 , 
                 ];
         array_push($options,  $success);

         $default=[ 'text' => '待繳費' ,
            'value' => 0 , 
         ];
         array_push($options,  $default);

         $canceled=[ 'text' => '已取消' ,
                     'value' => -1, 
                 ];
         array_push($options,  $canceled);

        return $options;
           
    }
    public function index($course_id , $user_id ,$status)
    {
        $signupList=$this->getAll()->with(['course','user.profile'])
                                ->where('status',$status); 
        
        
        if($course_id) $signupList=$signupList->where('course_id',$course_id);       

        if($user_id) $signupList=$signupList->where('user_id',$user_id);

        return $signupList;
    }
    public function store($course,$discount,$user_id,$updated_by,$date=null)
    {
          $values=[
                'course_id' => $course->id,
                'user_id' => $user_id,
                'status' => 0,
                'updated_by' => $updated_by,
          ];

          if($date){
              $values=array_add($values, 'date', $date);
          }else{
              $values=array_add($values, 'date', Carbon::now()->toDateString());
          }

          $tuitionValues=$this->getTuitionValues($course,$discount);
          $values=array_merge($values,$tuitionValues);

          $discountValues=$this->getDiscountValues($discount);
          $values=array_merge($values,$discountValues);

          $signup=Signup::create($values);
          return $signup;
     }
     public function update($signup,$course,$discount,$user_id,$updated_by,$date,$net_signup)
     {
          $values=[
                'course_id' => $course->id,
                'user_id' => $user_id,
                'updated_by' => $updated_by,
                'net_signup' => $net_signup
          ];

          if($date){
              $values=array_add($values, 'date', $date);
          }else{
              $values=array_add($values, 'date', Carbon::now()->toDateString());
          }

          $tuitionValues=$this->getTuitionValues($course,$discount);
          $values=array_merge($values,$tuitionValues);

          
          $discountValues=$this->getDiscountValues($discount);
          $values=array_merge($values,$discountValues);
        

          $signup->update($values);

          $signup->updateStatus();

          return $signup;
     }
     public function delete($id , $updated_by)
     {
         $signup = $this->findOrFail($id);
         $values=[
            'status' => -1 ,
            'removed' => 1,
            'updated_by' => $updated_by
         ];
        
         $signup->update($values);
     }

     public function getByUser($user_id)
     {
        return $this->getAll()->where('user_id', $user_id);
     }
     public function getByCourse($course_id)
     {
        return $this->getAll()->where('course_id', $course_id);
     }

     public function updateStatus($id)
     {
         $signup = $this->findOrFail($id);
         $signup->updateStatus();
         
     }
     public function getSummary($course_id)
     {
           $info = DB::table('signups')
                        ->where('removed',false)
                        ->where('course_id',$course_id)
                        ->select('status', DB::raw('count(*) as total'))
                        ->groupBy('status')->get();

            return $this->printSignupSummary($info);
            
     }
     public function printSignupSummary($info)
     {
            $success=0;
            $successItem = $info->filter(function($item) {
                return $item->status == 1;
            })->first();
            if($successItem) $success= $successItem->total;
            
            $default=0;
            $defaultItem = $info->filter(function($item) {
                return $item->status == 0;
            })->first();  
            if($defaultItem) $default= $defaultItem->total;

            $canceled=0;
            $canceledItem= $info->filter(function($item) {
                return $item->status == -1;
            })->first();      
            if($canceledItem) $canceled= $canceledItem->total;

            $total=$success + $default + $canceled;

            $summary=[
                'success'=> $success,
                'default'=> $default,
                'canceled'=> $canceled,
                'total' => $total
            ];
            return $summary;
     }

     private function getTuitionValues($course,$discount)
     {
          $tuition=$course->tuition;
          $cost=$course->cost;
          $points=0;
          if($discount){
             $points=$discount->points;
             $tuition=$tuition*$points/100;
          }
          $values=[
                'tuition' => $tuition,
                'cost' => $cost,
                'points' => $points,
          ];

         return $values;
     }  
     private function getDiscountValues($discount)
     {
         if(!$discount){
            return [
                'discount_id' => null,
                'discount' => null,
                'identity' => null
             ];
         }else{
            return [
                'discount_id' => $discount->id,
                'discount' => $discount->name,
                'identity' => $discount->identityName()
            ];
         }
         
     }  

     

     
   
    
}