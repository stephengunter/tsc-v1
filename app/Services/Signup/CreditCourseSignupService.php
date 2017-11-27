<?php
namespace App\Services\Signup;

use App\Signup;
use App\Course;
use App\User;
use App\Profile;
use App\Tuition;

use Carbon\Carbon;

use App\Repositories\Signups;
use App\Repositories\Discounts;
use App\Repositories\Payways;
use Exception;
use DB;

use App\Exceptions\RequestError;

class SignupService
{
    public function __construct(Signups $signups,Payways $payways, Discounts $discounts)
    {
        $this->signups=$signups;
        $this->payways=$payways;
        $this->discounts=$discounts;

        $this->statusOptions=array(
            [ 
               'text' => '已繳費' ,
               'value' => 1 , 
            ],
            [ 
               'text' => '待繳費' ,
               'value' => 0 , 
            ],
            [ 
               'text' => '已取消' ,
               'value' => -1 , 
            ],
        );
    }

    public function statusOptions()
    {
        return $this->statusOptions;
    }

    public function getPayways()
    {
        return $this->payways->getAll();
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
    public function getByCourseId($course_id)
    {
      
        return $this->signups->getByCourseId($course_id);
     
    }
    public function getByUserId($user_id)
    {
      
        return $this->signups->getByUserId($user_id);
     
    }
   

    public function store(Course $course, User $user, Signup $signup , Tuition $tuition=null)
    {
        $err=$this->canSignup($course, $user);
        
        if(!$tuition){
            $signup->discount_id=0;
            $signup->discount='';
            $signup->points=0;
            $signup->tuition=$course->tuition;
            $signup->cost=$course->cost;
        }

        $date =null;
        try {
            $date = Carbon::parse($signup->date);
        }
        catch (Exception $err) {
           
            throw new RequestError('signup.date','日期錯誤');
        }

      

        if($signup->discount_id){
            $discount=$this->countTuition($course,$signup->discount_id,$date);
            if($discount->tuition!=$signup->tuition){                    
                
                throw new RequestError('signup.tuition', '課程費用錯誤');
               
                
            }
             
            $signup->discount=$discount->name;
            $signup->points=$discount->points; 
        }

        if($tuition){
            $signup= DB::transaction(function() use($signup,$tuition) {
                $signup->save();
               
                $signup->tuitions()->save($tuition);
       
                return $signup;
                 
            });

            return $signup;
        }else{
            $signup->save();
        }

        return $signup;


    }
    public function update(Signup $signup, $values)
    {
        $signup->fill($values);

        $course=Course::findOrFail($signup->course_id);

        $date =null;
        try {
            $date = Carbon::parse($signup->date);
        }
        catch (Exception $err) {
           
            throw new RequestError('signup.date','日期錯誤');
        }
       
        if($signup->discount_id){
            $discount=$this->countTuition($course,$signup->discount_id,$date);
            if($discount->tuition!=$signup->tuition){                    
                
                throw new RequestError('signup.tuition', '課程費用錯誤');
               
                
            }
             
            $signup->discount=$discount->name;
            $signup->points=$discount->points; 
        } else{
       
            $signup->discount_id=0;
            $signup->discount='';
            $signup->points=0;
            $signup->tuition=$course->tuition;
            $signup->cost=$course->cost;
        }

        $signup->save();
        return $signup;
    }

    public function canSignup(Course $course, User $user)
    {
        if($user->id){
            $validSignups=$this->getValidSignups($course->id)->where('user_id',$user->id)->get();
            if(count($validSignups) ) {
                
                throw new RequestError('signup.user_id','此學員已報名過此課程了');
            }
        }
      



        return true;
      
    }

   

    public function getValidSignups($course_id)
    {
      //沒有被刪除/取消的有效報名
      return $this->getByCourseId($course_id)->where('status' , '>','-1' );
      
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