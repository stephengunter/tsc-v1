<?php
namespace App\Services\Signup;

use App\Signup;
use App\Course;
use App\User;
use App\Profile;
use App\Tuition;
use App\Discount;
use App\Bill;
use App\Student;

use App\Repositories\Courses;
use App\Repositories\Discounts;
use App\Repositories\Payways;
use App\Repositories\Signups;
use App\Repositories\Users;
use App\Repositories\Terms;
use App\Repositories\Centers;


use Carbon\Carbon;
use Exception;
use DB;
use App\Support\Helper;
use App\Exceptions\RequestError;

use App\Events\SignupCreated;

class SignupService
{
    public function __construct(Signups $signups,Payways $payways, Discounts $discounts,Courses $courses, 
                                Users $users,Terms $terms , Centers $centers)
   
   
    {
       
        $this->signups=$signups;
        $this->payways=$payways;
        $this->discounts=$discounts;
        $this->courses=$courses;

        $this->users=$users;
        $this->terms=$terms;
        $this->centers=$centers;

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

    public function updateUser($userValues,$profileValues, $user)
    {
        return $this->users->updateUserAndProfile($userValues,$profileValues, $user);
    }

    public function statusOptions()
    {
        return $this->statusOptions;
    }

    public function termOptions()
    {
        return $this->terms->options();
    }

    public function centerOptions()
    {
        return $this->centers->options();
    }

    public function userOptions()
    {
        $userList=$this->users->getAll()->with('profile')->get();
        $userOptions=$this->users->optionsConverting($userList);

        return $userOptions;
    }

    public function getPayways()
    {
        return $this->payways->getAll();
    }
    

    public function  getReviewedCourses($term_id,$center_id)
    {
        
        return $this->courses->getReviewedCourses($term_id,$center_id);
    }

    public function  getCanSignupCourses($term_id,$center_id)
    {
        return $this->getReviewedCourses($term_id,$center_id);
    }

    public function  getCourseOptions()
    {
        $courseList=$this->courses->canSignupCourses();
        if($courseList->count()){
            $courseOptions=$this->courses->optionsConverting($courseList->get());
        }
    }

    

    public function  initPayBill($signups)
    {
        $total = $signups->sum(function ($item) {
            return $item->course->tuition;
        });
        
        $bill=Bill::init();

        $bill['total']=$total;

        $signupIds=$signups->pluck('id')->toArray();

        $bill['signup_ids']=Helper::strFromArray($signupIds);

        return $bill;
    }

    public function  initOnlinePayBill($signups)
    {
        $total = $signups->sum(function ($item) {
            return $item->course->tuition;
        });
        
        $bill=Bill::init();

        $bill['total']=$total;

        $courseCount=count($signups);
         //判斷折扣
        $discount=$this->autoDecideDiscount($courseCount);

        if($discount){
            $points=$discount->points;

            $bill['discount_id']=$discount->id;
            $bill['discount']=$discount->name;
            $bill['points']=$points;

            $bill['amount']=round($points * $total/100);
        }else{
            $bill['discount_id']='';
            $bill['discount']='';
            $bill['points']=0;

            $bill['amount']=$total;
        }


        return $bill;
        
    }
    
    public function  getDiscountOptions(int $center_id ,$date=null)
    {
        return $this->discounts->getDiscountOptions($center_id,$date);
        
    } 



    public function  getDiscountOptionsByCourse(Course $course)
    {
        $validDiscounts=$this->discounts->getValidDiscounts($course,$date);
        return $this->discounts->optionsConverting($validDiscounts);
        
    } 

    public function saveBill(Bill $bill , $signups)
    {
        $bill->save();

        foreach($signups as $signup){
            $signup->bill_id=$bill->id;
            $signup->save();
        }

        return $bill;
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

    
    public function getSummary($course_id)
    {
          $info = DB::table('signups')
                       ->where('removed',false)
                       ->where('course_id',$course_id)
                       ->select('status', DB::raw('count(*) as total'))
                       ->groupBy('status')->get();

           return $this->printSignupSummary($info);
           
    }
    public function getSummaryByUser($user_id)
    {
          $info = DB::table('signups')
                       ->where('removed',false)
                       ->where('user_id',$user_id)
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

    public function storeBillAndSignups(Bill $bill, Tuition  $tuition,  $signups)
    {
        $bill= DB::transaction(function() use($bill,$tuition,$signups) {
 
             $bill->save();
             

             if(!$tuition->date)  $tuition->date=Carbon::today();
           
             $bill->tuitions()->save($tuition);
 
             foreach($signups as $signup){
                   
                if(!$signup->date)  $signup->date=Carbon::today();
                $bill->signups()->save($signup);
             }
 
             $bill->updateStatus();
 
             return $bill;
             
        });

       

        return $bill;
    }
   
    //單一課程報名
    // public function store(Course $course, User $user, Signup $signup , Tuition $tuition=null)
    // {
    //     throw new RequestError('','');

    //     $this->canSignup($course, $user, $signup->net_signup);
        
    //     if(!$tuition){
    //         $signup->discount_id=0;
    //         $signup->discount='';
    //         $signup->points=0;
    //         $signup->tuition=0;
    //         $signup->cost=0;
    //     }

    //     $date =null;
    //     try {
    //         $date = Carbon::parse($signup->date);
    //     }
    //     catch (Exception $err) {
           
    //         throw new RequestError('signup.date','日期錯誤');
    //     }

      

    //     if($signup->discount_id){
    //         $discount=$this->countTuition($course,$signup->discount_id,$date);
    //         if($discount->tuition!=$signup->tuition){                    
                
    //             throw new RequestError('signup.tuition', '課程費用錯誤');
    //         }
             
    //         $signup->discount=$discount->name;
    //         $signup->points=$discount->points; 
    //     }

    //     if($tuition){
    //         $signup= DB::transaction(function() use($signup,$tuition) {
    //             $signup->save();
               
    //             $signup->tuitions()->save($tuition);

                
    //             return $signup;
                 
    //         });

           
    //     }else{
    //         $signup->save();
    //     }

    //     //$signup->updateStatus();
    //     $course->updateStatus();
      
    //     //event(new SignupCreated($signup));

    //     return $signup;


    // }

    public function delete(Signup $signup, User $current_user)
    {
        $this->canDelete($signup, $current_user);
        $signup->removed=1;
        $signup->save();

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
        }

        $signup->save();
        return $signup;
    }

    public function isUserHasSignuped($course_id,$user_id)
    {
        $validSignups=$this->getValidSignups($course_id)->where('user_id',$user_id)->get();
        if(count($validSignups) ) {
            
           return true;
        }

        return false;
    }

    public function canSignup(Course $course, User $user, $isNetSignup)
    {
        
        if(!$course->canSignup($isNetSignup))
        {
            throw new RequestError('signup.course_id','此課程目前無法報名');
        }
        if($user->id){

            
            if($course->hasSignupBy($user->id)) throw new RequestError('signup.user_id','此學員已報名過此課程了');


            // $validSignups=$this->getValidSignups($course->id)->where('user_id',$user->id)->get();
            // if(count($validSignups) ) {
                
            //     throw new RequestError('signup.user_id','此學員已報名過此課程了');
            // }
        }
      



        return true;
      
    }
    public function canDelete(Signup $signup, User $user)
    {
         

        if(!$signup->canDeleteBy($user))
        {
            throw new RequestError('signup.user_id','此報名無法刪除');
        }
        // if($user->id){
        //     $validSignups=$this->getValidSignups($course->id)->where('user_id',$user->id)->get();
        //     if(count($validSignups) ) {
                
        //         throw new RequestError('signup.user_id','此學員已報名過此課程了');
        //     }
        // }
      
       
      
    }
    public function isUserDataComplete($user)
    {
        if(!$user->profile->fullname) return false;
        if(!$user->profile->SID) return false;
        if(!$user->profile->dob) return false;
        
        if(!$user->email) return false;
        if(!$user->phone) return false;

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

    public function payByCreditCard($payway_id)
    {
        return $this->payways->payByCreditCard($payway_id);
    }

    public function payBySeven($payway_id)
    {
        return $this->payways->payBySeven($payway_id);
    }
}