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
use DB;
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

      $billValues=$request->getBillValues($updated_by);
      $tuitionValues=$request->getTuitionValues($updated_by); 
      $signups=$request->getSignups();
      

      $bill= DB::transaction(function() use($billValues,$tuitionValues,$signups) {
           
            $date=Carbon::today();

            $bill=Bill::create($billValues);

            $tuitionValues['date']=$date;
            $tuition=new Tuition($tuitionValues);  
            
            $bill->tuitions()->save($tuition);

            foreach($signups as $signup){
                  $signup['bill_id']=$bill->id;
                  $signup['date']=$date;
      
                  $bill->signups()->save(new Signup($signup));
            }

            $bill->updateStatus();

            return $bill;
            
      });

      

      return response() ->json($bill);
         
   }

   public function show($id)
   {
       $current_user=$this->currentUser();
      
       $bill=Bill::with(['signups','tuitions'])->findOrFail($id);

       foreach($bill->signups as $signup){
            $signup->course->fullName();
       }

       foreach($bill->tuitions as $tuition){
          $tuition->populateViewData();
       }

       return response()->json([ 'bill' => $bill ]);

   }

   public function discountOptions()
   {
      $center_id=(int)request()->center;
      $date=null;
      try {
          $date = Carbon::parse(request()->date);
      }
      catch (Exception $err) {
          $date=Carbon::today();
      }
      
      Center::findOrFail($center_id);
      
      $discounts= $this->signupService->getDiscountOptions($center_id,$date);

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
    public function print($id)    
    {
       
        $current_user=$this->currentUser();
    
        $bill=Bill::with(['signups'])->findOrFail($id);

        dd($bill);
        //$signup->course->center->contactInfo=$signup->course->center->contactInfo();
        
        $invoiceMoney=$bill->invoiceMoney();
        $date='';
        $payBy='';

        if($invoiceMoney > 0){
            $incomeRecord=$signup->incomeRecords()
                                ->orderBy('date','desc')
                                ->first();
            $date=$incomeRecord->date;
            $payBy=$this->payways->textPayBy($incomeRecord->pay_by);
        

        }

        $invoice=[
            'money'=> Helper::formatMoney($invoiceMoney),
            'date' => $date,
            'payBy' => $payBy
        ];

        $signup->tuition=Helper::formatMoney($signup->tuition);
    
        $title=$signup->course->name . ' 課程費用收據'; 
        $pdf = PDF::loadView('signups.invoice', [
                                'title' => $title,
                                'signup' => $signup,
                                'invoice' => $invoice
                            ]);

                        
        return $pdf->stream();
    }
   
   private function creditCardPay(Bill $bill)
   {
         return true;
   }
    
    
   

    

   
    
}
