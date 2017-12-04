<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;

use App\Http\Requests\Signups\SignupRequest;
use App\Http\Requests\Signups\OnlineSignupRequest;


use App\Services\Signup\SignupService;

use App\Signup;
use App\User;
use App\Course;
use App\Tuition;
use App\Bill;

use App\Support\Helper;

use App\Events\SignupChanged;
use Carbon\Carbon;
use PDF;
use Exception;

use App\Exceptions\RequestError;

class PayController extends BaseController
{
    
   public function __construct(SignupService $signupService)                       
   {
      $this->middleware('auth:api');
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

   public function index()
   {
      
      $user= $this->currentUser();

      $signups=$this->getCanPaySignups($user->id);
                              
      foreach ($signups as $signup) {
            $signup->populateViewData();    
      }       
      
      
      $bill=$this->signupService->initOnlinePayBill($signups);

      return response() ->json([ 
                                 'signups' => $signups,
                                  'bill' => $bill        
                              ]);
      
   }

    
    
   public function store(Request $request)
   {
      $user=$this->currentUser();
      $updated_by=$user->id;

      $postedBill=$request['bill'];
      
      $signups=$this->getCanPaySignups($user->id);
      $bill=$this->signupService->initOnlinePayBill($signups);

      
      if(floatval($postedBill['amount']) != floatval($bill['amount'])){
            throw new RequestError('bill.amount','金額錯誤');
      }
      
      $bill['pay_way']=$postedBill['pay_way'];
    
      $bill=new Bill($bill);

      if($this->signupService->payBySeven($bill->pay_way)){
            //便利商店繳費
      }else{
          $success=$this->creditCardPay($bill);

          $bill->status=1;  //已繳費
      }



      $bill=$this->signupService->saveBill($bill , $signups);

      return response() ->json($bill);
         
   }
   
   private function creditCardPay(Bill $bill)
   {
         return true;
   }
    
    
   

    

   
    
}
