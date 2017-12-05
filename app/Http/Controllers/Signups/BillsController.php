<?php

namespace App\Http\Controllers\Signups;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

use App\Http\Requests\Signups\PayRequest;

use App\Services\Signup\SignupService;

use App\Signup;
use App\User;
use App\Course;
use App\Tuition;
use App\Bill;
use App\Center;

use App\Support\Helper;

use App\Events\SignupChanged;
use Carbon\Carbon;
use PDF;
use Exception;

use App\Exceptions\RequestError;

class BillsController extends BaseController
{
    
   public function __construct(SignupService $signupService)                       
   {
      
      $this->signupService=$signupService;

   }

   private function getCanPaySignups($user_id)
   {
      $signups=$this->signupService->getByUserId($user_id)
                                   ->orderBy('date','desc')
                                   ->get(); 
       
      $signups = $signups->filter(function ($item) {
            return $item->canPay();
      }); 

      return $signups;

      
   }

   public function create()
   {
      $request = request();
      $user_id=(int)$request->user;

      $user=User::findOrFail($user_id);
      

      $signups=$this->getCanPaySignups($user->id);

      $bill=[];
      if(count($signups)){
          foreach ($signups as $signup) {
             $signup->populateViewData();    
          } 

          $bill=$this->signupService->initPayBill($signups);   
      }else{
          $bill=Bill::init();
      }
      

      

      $tuition=Tuition::initialize();
      
      $payways=$this->signupService->getPayways();
      
      return response()->json([
            
            'signups' => $signups,
            'bill' => $bill,
            'tuition' => $tuition,
            'payways' => $payways

        ]);
      
      // $date=Carbon::today();
      
      // $course=$signups[0]->course;
      // $discounts=$this->signupService->getDiscountOptions($course,$date);
      // return response() ->json([ 
      //                             'signups' => $signups,
      //                             'bill' => $bill ,
      //                             'discounts'  => $discounts ,
      //                             'tuition' => $tuition,
      //                             'payways' => $payways   
      //                         ]);
      
   }

    
    
   public function store(PayRequest $request)
   {
      $user=$this->currentUser();
      $updated_by=$user->id;

      $date=Carbon::today();

      $bill=new Bill($request->getBillValues($updated_by));
      $bill->save();
      

      $tuitionValues=$request->getTuitionValues($updated_by); 
      $tuitionValues['date']=$date;
      $tuition=new Tuition($tuitionValues);  
      $bill->tuitions()->save($tuition);

   
      $signups=$request->getSignups();
      foreach($signups as $signup){
            $signup['bill_id']=$bill->id;
            $signup['date']=$date;
            Signup::create($signup);
      }
      

      

      

      return response() ->json($bill);
         
   }

   public function discountOptions()
   {
      $center_id=(int)request()->center;
      
      Center::findOrFail($center_id);
      
      $discounts= $this->signupService->getDiscountOptions($center_id);

      return response()->json([ 'discounts' => $discounts ]); 
      
   }

//    public function options()
//    {
       
//        $date=null;
//        try {
//            $date = Carbon::parse(request()->date);
//        }
//        catch (Exception $err) {
//            $date=Carbon::today();
//        }

//        $activeDiscounts=null;

//        $course_id=(int)request()->course;
//        $course=$this->courses->findOrFail($course_id);
//        $activeDiscounts=$this->discounts->getValidDiscounts($course,$date);

       
//        $options=$this->discounts->optionsConverting($activeDiscounts);
     

//        return response()->json([ 'options' => $options ]);  
//    }
   
   private function creditCardPay(Bill $bill)
   {
         return true;
   }
    
    
   

    

   
    
}
