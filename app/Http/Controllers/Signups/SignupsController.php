<?php

namespace App\Http\Controllers\Signups;

use App\Http\Controllers\BaseController;

use Illuminate\Http\Request;
use App\Http\Requests\Signups\SignupRequest;

use App\Repositories\Courses;
use App\Repositories\Discounts;
use App\Repositories\Payways;
use App\Repositories\Signups;
use App\Repositories\Users;
use App\Repositories\Terms;
use App\Repositories\Centers;

use App\Signup;
use App\User;
use App\Course;

use App\Support\Helper;
use App\Http\Middleware\CheckAdmin;

use App\Events\SignupChanged;
use PDF;

class SignupsController extends BaseController
{
    protected $key='signups';
    public function __construct(Courses $courses, Discounts $discounts,Payways $payways,
                                Terms $terms , Centers $centers, 
                                  Signups $signups, Users $users, CheckAdmin $checkAdmin) 
                               
    {
         $exceptAdmin=[];
         $allowVisitors=[];
		 $this->setMiddleware( $exceptAdmin, $allowVisitors);

		 $this->courses=$courses;
         $this->discounts=$discounts;
         $this->payways=$payways;
         $this->terms=$terms;
         $this->centers=$centers;
         $this->signups=$signups;
         $this->users=$users;

         $this->setCheckAdmin($checkAdmin);

	}

    

    public function index()
    {
        $request = request();
          
        if(!$request->ajax()){
            $menus=$this->menus($this->key);            
            return view('signups.index')
                    ->with(['menus' => $menus]);
        }  

        $signupList=[];
        $course_id=(int)$request->course; 
        if($course_id > 0){
            $signupList= $this->signups->getByCourse($course_id);
            $status=(int)$request->status;
            if( $status >= -1  && $status <=1 ){
                $signupList=$signupList->where('status',$status);
            }
            $signupList=$signupList->with(['course','user.profile'])
                                    ->filterPaginateOrder();
            $summary=$this->signups->getSummary($course_id);   

            return response() ->json([ 'model' => $signupList,
                                       'summary' => $summary
                                      ]); 
        }


        $user_id=(int)$request->user;
        $signupList= $this->signups->getByUser($user_id)
                                            ->with(['course','user.profile']);

        return response() ->json(['model' => $signupList->filterPaginateOrder()  ]); 
       
    }

    public function create()
    {
         $request = request();
        
         $course_id=(int)$request->course; 
         $user_id=(int)$request->user; 

         $course=null;
         if($course_id>0){
            $course= $this->courses->findOrFail($course_id);
         }
         $courseOptions=$this->getCourseOptions($course);
         if(empty($courseOptions)) {
             return   response()->json(['msg' => '無課程可報名' ]  ,  422);   
         }
         
         $userOptions =[];
         $user=null;
         if($user_id > 0){
            $user= $this->users->findOrFail($user_id);
            $user->profile;
         }else{
             $userOptions = $this->getUserOptions($user);
         }       
         

         $signup=Signup::initialize($user_id,$course_id);
         
         $discountOptions=$this->getDiscountOptions();

         return response()
            ->json([
                'courseOptions' => $courseOptions,
                'userOptions' => $userOptions,
                'discountOptions' => $discountOptions,
                'signup' => $signup,
                'course' => $course,
                'user' => $user

            ]);
         
    }
    public function indexOptions()
    {
        $termOptions=$this->terms->options();
        $centerOptions=$this->centers->options();

        return response()
            ->json([
                'termOptions' => $termOptions,
                'centerOptions' => $centerOptions,
            ]);

    }
    
   
    
    public function store(SignupRequest $request)
    {  
         $current_user=$this->currentUser();
         $updated_by=$current_user->id;
         $values=$request->getValues($updated_by);

         $user_id=$values['user_id'];
         $user=$this->users->findOrFail($user_id);

         $course_id=$values['course_id'];
         $course=$this->courses->findOrFail($course_id);

         $discount=null;
         $discount_id=(int)$values['discount_id'];
         if($discount_id > 0){
            $discount=$this->discounts->findOrFail($discount_id);
           
         }

         if($course->hasSignupBy($user->id)){
              $errMsg= ['此學員已報名過此課程了'] ;
              return   response()->json(['signup.user_id' => $errMsg ]  ,  422);
         }

         $date=$values['date'];
         
         $signup=$this->signups->store($course,$discount,$user_id,$updated_by,$date);
         
         return response()->json($signup);
            
    }
    public function edit($id)
    {
        $signup=$this->signups->findOrFail($id);  
        $current_user=$this->currentUser();
        if(!$signup->canEditBy($current_user)){
            return  $this->unauthorized();  
        }  

        $course_id=$signup->course_id;
        $course= Course::find($course_id);
         
        $user_id=$signup->user_id;
        
        $user= User::with('profile')->find($user_id);
  
        $discountOptions=$this->getDiscountOptions();

         return response()
            ->json([
                'discountOptions' => $discountOptions,
                'signup' => $signup,
                'course' => $course,
                'user' => $user

            ]);
    }
    public function update(SignupRequest $request, $id)
    {
         $signup=$this->signups->findOrFail($id);  
         $current_user=$this->currentUser();
         if(!$signup->canEditBy($current_user)){
            return  $this->unauthorized();  
         } 

         $updated_by=$current_user->id;
         $values=$request->getValues($updated_by);
        
         $course_id=$values['course_id'];
         $course=$this->courses->findOrFail($course_id);

         $user=null;
         $user_id=(int)$values['user_id'];
         if($user_id != (int)$signup->user_id){
             $user=$this->users->findOrFail($user_id);
         }

         $discount=null;
         $discount_id=(int)$values['discount_id'];
         if($discount_id > 0){
            $discount=$this->discounts->findOrFail($discount_id);
           
         }

         $date=$values['date'];
         $net_signup= $values['net_signup'];
         
         $signup=$this->signups->update($signup,$course,$discount,
                                        $user_id,$updated_by,$date,$net_signup);
         
         
         
         
         event(new SignupChanged($signup));
         
         return response()->json($signup);
            
    }
    
    public function show($id)
    {
        if(!request()->ajax()){
            $menus=$this->menus($this->key);            
            return view('signups.details')
                    ->with([ 'menus' => $menus,
                              'id' => $id     
                        ]);
         }  

        $current_user=$this->currentUser();

        $signup=Signup::with('course','user.profile')->findOrFail($id);
        if(!$signup->canViewBy($current_user)){
            return  $this->unauthorized(); 
        }

        $signup->canEdit=$signup->canEditBy($current_user);
        $signup->canDelete=$signup->canDeleteBy($current_user);        
        $signup->hasRefund=$signup->hasRefund();

        $invoiceMoney=$signup->invoiceMoney();
        $signup->hasInvoice=$invoiceMoney > 0;

        return response()->json([ 'signup' => $signup ]);

    }
    public function destroy($id)
    {
        $signup=$this->signups->findOrFail($id); 
        $current_user=$this->currentUser();

        if(!$signup->canDeleteBy($current_user)){
            return  $this->unauthorized();
        }    

        $this->signups->delete($id, $current_user->id);

        return response()
                ->json([
                    'deleted' => true
                ]);
    }
    public function print($id)
    {
        $current_user=$this->currentUser();
     
        $signup=Signup::with('course.center','user.profile')->findOrFail($id);
        $signup->course->center->contactInfo=$signup->course->center->contactInfo();
        
        $invoiceMoney=$signup->invoiceMoney();
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
    public function getByUser($user)
    {
        $signupList=$this->signups->getByUser($user);
        if($signupList->count()){
            $signupList=$signupList->orderBy('created_at','desc')->get();
        }

        foreach($signupList as $signup){
            $signup->canCancel=$signup->canCancel();
        }
        return response()->json([
                            'signupList' => $signupList
                        ]);
                            
    }
    public function statusOptions()
    {
        $options=$this->signups->statusOptions();
        return response()
                ->json([
                    'options' => $options
                ]);
    }

    private function check($course, $user, $discount)
    {
        if($course){
            if(!$course->canSignup()){
              return   '此課程目前無法報名' ;
            }
        }

        if($user){
            if($course->hasSignupBy($user->id)){
              return   '您已報名過此課程了' ;
            }
        }
         
        if($discount){
               if(!$discount->active){
                return   '折扣已過期' ;
            }
        }

        return '';
    }
    private function getDiscountOptions()
    {
        $activeDiscounts=$this->discounts->activeDiscounts()->get();
        return $this->discounts->optionsConverting($activeDiscounts);
    }
    private function  getCourseOptions($course)
    {
         $courseOptions=[];
         
         if($course){
             $item=$course->toOption();
             array_push($courseOptions,  $item);
         }else
         {
             $courseList=$this->courses->canSignupCourses();
             if($courseList->count()){
                 $courseOptions=$this->courses->optionsConverting($courseList->get());
             }
            
         }

         return $courseOptions;
    }

    private function  getUserOptions($user)
    {
         $userOptions=[];
        
         if($user){           
             $item=$user->profile->toOption();             
             array_push($userOptions,  $item);
         }else
         {
             $userList=$this->users->getAll()->with('profile')->get();
           
             $userOptions=$this->users->optionsConverting($userList);
             
         }

         return $userOptions;
    }

   
    
}
