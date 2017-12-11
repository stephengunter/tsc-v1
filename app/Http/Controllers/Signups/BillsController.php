<?php

namespace App\Http\Controllers\Signups;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

use App\Http\Requests\Signups\PayRequest;

use App\Services\Signup\SignupService;
use App\Services\Signup\DiscountService;

use App\Signup;
use App\User;
use App\Course;
use App\Tuition;
use App\Bill;
use App\Center;
use App\Discount;

use App\Support\Helper;

use App\Events\SignupChanged;
use Carbon\Carbon;
use PDF;
use DB;
use Exception;

use App\Exceptions\RequestError;

class BillsController extends BaseController
{
    
   public function __construct(DiscountService $discountService,SignupService $signupService)                       
   {
      $this->discountService=$discountService;
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

        $signups=$request->getSignups();

        //網路報名的標準
        $isNetSignup=true;

        foreach($signups as $signup){
            $course=Course::findOrFail($signup->course_id);
            $user=User::findOrFail($signup->user_id);
            $this->signupService->canSignup($course, $user, $isNetSignup);
        }


        $billValues=$request->getBillValues($updated_by);
        $bill=new Bill($billValues);

        $discount=Discount::find($bill->discount_id);
        if($discount){
            $bill->discount=$discount->name;
        }

        $tuitionValues=$request->getTuitionValues($updated_by);
        $tuition=new Tuition($tuitionValues);

        $bill= $this->signupService->storeBillAndSignups($bill,$tuition,$signups);
        
        if($bill->identity_id){
            foreach($signups as $signup){
                $student=$signup->getStudent();
                $student->identity_id=$bill->identity_id;
                $student->save();
            }
            
        }
        
        
        return response()->json($bill);
         
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
      $course_id=(int)request()->course_id;
      $course_count=(int)request()->course_count;
      $date=null;
      try {
          $date = Carbon::parse(request()->date);
      }
      catch (Exception $err) {
          $date=Carbon::today();
      }

      $course=Course::findOrFail($course_id);
      
      
      $term=$course->term;  //default term
      $center_id=$course->center_id;
      $discounts= $this->discountService->getDiscountOptions( $center_id, $course_count, $term, $date);

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
