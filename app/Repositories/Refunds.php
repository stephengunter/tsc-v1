<?php

namespace App\Repositories;

use App\Refund;
use App\Signup;
use App\Course;
use App\Support\Helper;
use DB;

class Refunds 
{
    public function getAll()
    {
         return Refund::where('removed',false);
    }

    

    public function find($id)
    {
        $refund= Refund::findOrFail($id);
        if($refund->removed) return null;
        return $refund;
       
    }
    public function findOrFail($id)
    {
       return Refund::findOrFail($id);
       
    }
    
    public function getById($signup_id)
    {
        return $this->getAll()->where('signup_id', $signup_id)->first();
           
    }
    public function getBySignupIds(array $signup_ids)
    {
        return $this->getAll()->whereIn('signup_id', $signup_ids);
           
    }
    
    
    public function store($values,$signup)
    {
         $signup_id=$signup->id;
         $record=Refund::find($signup_id);
         if($record){
             $record->delete();
         }

         $number=$this->generateNumber($signup);

         $refund=new Refund($values);
         $refund->number=$number;
         $signup->refund()->save($refund);

         return $signup->refund;
     }
     
     public function delete($id , $updated_by)
     {
         $refund = $this->findOrFail($id);
         $values=[
            'removed' => 1,
            'updated_by' => $updated_by
         ];
        
         $refund->update($values);
     }
     public function updateStatus($id)
     {
         $refund = $this->findOrFail($id);
         $refund->updateStatus();
         
     }
     public function statusOptions()
     {
         $options=[];
         $default=[ 'text' => '待審核' ,
                     'value' => -1 , 
                 ];
         array_push($options,  $default);

         $processing=[ 'text' => '審核中' ,
            'value' => 0 , 
         ];
         array_push($options,  $processing);

         $completed=[ 'text' => '已完成' ,
                     'value' => 1, 
                 ];
         array_push($options,  $completed);

        return $options;
           
     }
     public function getSummary(array $signup_ids)
     {
           $info = DB::table('refunds')
                        ->where('removed',false)
                        ->whereIn('signup_id', $signup_ids)
                        ->select('status', DB::raw('count(*) as total'))
                        ->groupBy('status')->get();
           
            
            $default=0;
            $defaultItem = $info->filter(function($item) {
                return $item->status == -1;
            })->first();  
            if($defaultItem) $default= $defaultItem->total;

            $processing=0;
            $processingItem= $info->filter(function($item) {
                return $item->status == 0;
            })->first();      
            if($processingItem) $processing= $processingItem->total;

            $success=0;
            $successItem = $info->filter(function($item) {
                return $item->status == 1;
            })->first();
            if($successItem) $success= $successItem->total;

            $total=$success + $default + $processing;

            $summary=[
                'success'=> $success,
                'default'=> $default,
                'processing'=> $processing,
                'total' => $total
            ];
            return $summary;
     }
     
     public static function generateNumber(Signup $signup)
     {
        $course=Course::find($signup->course_id);
        $signupIds = $course->signups()->get()->pluck('id');
        $refundCount=Refund::whereIn('signup_id',$signupIds)->count();
        $count=$refundCount+1;

        $countString='';
         if($count < 10){
            $countString= "00" . $count;
         } else if($count<100){
             $countString= "0" . $count;
         }else{
             $countString=$count;
         }
         
         return $course->number . 'R'. $countString;
     }

     

     
   
    
}