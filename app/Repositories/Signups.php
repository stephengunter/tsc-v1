<?php

namespace App\Repositories;

use App\Signup;
use App\Course;
use App\Discount;
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
    public function netSignup($course,$discount,$user_id,$updated_by)
    {
        $values=$this->getSignupValues($course->id,$user_id,$updated_by);
        $values['net_signup']=1;

        $tuition=$course->tuition;
        $cost=$course->cost;
        $tuitionValues=$this->getTuitionValues($tuition, $cost , $discount);
        $values=array_merge($values,$tuitionValues);
        
        
        $discountValues=$this->getDiscountValues($discount);
        $values=array_merge($values,$discountValues);

        // $values=[
        //         'course_id' => $course->id,
        //         'user_id' => $user_id,
        //         'status' => 0,
        //         'updated_by' => $updated_by,
        //         'net_signup' => 1
        // ];
          
        // $values=array_add($values, 'date', Carbon::now()->toDateString());
        

        // $tuitionValues=$this->getTuitionValues($course,$discount);
        // $values=array_merge($values,$tuitionValues);

        // $discountValues=$this->getDiscountValues($discount);
        // $values=array_merge($values,$discountValues);

        $signup=Signup::create($values);
        return $signup;
    }

    public function createGroupSignup(int $parent_course,array $sub_course_ids,$discount,$user_id,$updated_by,$date=null)
    {
        $values=$this->getSignupValues($parent_course,$user_id,$updated_by,$date);
        $discountValues=$this->getDiscountValues($discount);
        $values=array_merge($values,$discountValues);

       

        $sub_signup_values= collect([]);
      
        for($i = 0; $i < count($sub_course_ids); ++$i){
            $sub_signup_values->push($this->getSignupValues($sub_course_ids[$i],$user_id,$updated_by,$date));
        }

        $signup=DB::transaction(function() 
        use($values ,$sub_signup_values){
            $signup=Signup::create($values);
            for($i = 0; $i < count($sub_signup_values); ++$i){
                $sub_signup=new Signup($sub_signup_values[$i]); 
                $sub_signup->parent=$signup->id;
                $sub_signup->save();
            }

            return $signup;

        });
        
        
        $sub_signups_courses= $signup->subSignupCourses();
        $tuition=$sub_signups_courses->sum('tuition');
        $cost=$sub_signups_courses->sum('cost');
       
        $tuitionValues=$this->getTuitionValues($tuition, $cost , $discount);
       
        

        $signup->update($tuitionValues);
        return $signup;
       
    }
    public function updateGroupSignup(Signup $signup,$discount,$user_id,$updated_by,$date,$net_signup , array $sub_course_ids)
    {
        $values=$this->getSignupValues($signup->course_id,$user_id,$updated_by,$date);
        $discountValues=$this->getDiscountValues($discount);
        $values=array_merge($values,$discountValues);

        $subSignups=$signup->subSignups()->get();
        for($i = 0; $i < count($subSignups); ++$i){
            $subSignup=$subSignups[$i];
            if(!in_array( $subSignup->course_id , $sub_course_ids)){
                $subSignup->delete();
            }
        }

        $sub_signup_values= collect([]);

        $subSignupCourseIds=$signup->subSignupCourseIds();
        for($i = 0; $i < count($sub_course_ids); ++$i){
            if(!in_array( $sub_course_ids[$i] , $subSignupCourseIds)){
                $sub_signup_values->push($this->getSignupValues($sub_course_ids[$i],$user_id,$updated_by,$date));
            }
        }

        if(count($sub_signup_values)){
            for($i = 0; $i < count($sub_signup_values); ++$i){
                $sub_signup=new Signup($sub_signup_values[$i]); 
                $sub_signup->parent=$signup->id;
                $sub_signup->save();
            }
        }
       
        
        $sub_signups_courses= $signup->subSignupCourses();
        $tuition=$sub_signups_courses->sum('tuition');
        $cost=$sub_signups_courses->sum('cost');
       
        $tuitionValues=$this->getTuitionValues($tuition, $cost , $discount);
       
        

        $signup->update($tuitionValues);
        return $signup;
       
    }

    private function getSignupValues($course_id,$user_id,$updated_by, $date=null)
    {
        
        $values=[
            'course_id' => $course_id,
            'user_id' => $user_id,
            'status' => 0,
            'updated_by' => $updated_by,
        ];

        if($date){
            $values=array_add($values, 'date', $date);
        }else{
            $values=array_add($values, 'date', Carbon::now()->toDateString());
        }

        return $values;

        
    }

    public function store( $course, $discount,$user_id,$updated_by,$date=null)
    {
        $values=$this->getSignupValues($course->id,$user_id,$updated_by,$date);
        $tuition=$course->tuition;
        $cost=$course->cost;
        $tuitionValues=$this->getTuitionValues($tuition, $cost , $discount);
        $values=array_merge($values,$tuitionValues);
        
        
        $discountValues=$this->getDiscountValues($discount);
        $values=array_merge($values,$discountValues);

        $signup=Signup::create($values);
        return $signup;
     }
     public function update(Signup $signup,$discount,$user_id,$updated_by,$date,$net_signup)
     {
        $course=$signup->course;
        $values=$this->getSignupValues($signup->course_id,$user_id,$updated_by,$date);
        $values['net_signup']=$net_signup;
        
       
        $tuition=$course->tuition;
        $cost=$course->cost;
        $tuitionValues=$this->getTuitionValues($tuition, $cost , $discount);
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
     public function cancel($id , $updated_by)
     {
         $signup = $this->findOrFail($id);
         $values=[
            'status' => -1 ,
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

     private function countGroupTuitionValues($parent_course_id,$discount)
     {
        $sub_signups=$this->getAll()->where('parent',$parent_course_id)->get();
        $tuition=$sub_signups->sum('tuition');
        $cost=$sub_signups->sum('cost');
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

     private function getTuitionValues($tuition, $cost ,Discount $discount=null)
     {
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
     private function getDiscountValues(Discount $discount=null)
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