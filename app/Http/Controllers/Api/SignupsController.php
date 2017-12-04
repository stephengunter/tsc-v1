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

use App\Support\Helper;

use App\Events\SignupChanged;
use Carbon\Carbon;
use PDF;
use Exception;

use App\Exceptions\RequestError;

class SignupsController extends BaseController
{
    
    public function __construct(SignupService $signupService)                       
    {
        $this->middleware('auth:api');
        $this->signupService=$signupService;

    }

    public function index()
    {
        $request = request();
        $status=(int)$request['status'];
        $user=$this->currentUser();

        $signups=$this->signupService->getByUserId($user->id)  
                                     ->with(['course','user.profile']);
                                     
                                     
        if($status){
            // 顯示全部
        }else{
             // 只顯示待繳費的
            $signups=$signups->where('status', 0);
        }

        $signups=$signups->orderBy('date','desc')->get();
                                
        foreach ($signups as $signup) {
              $signup->populateViewData();    
        }                       

        return response() ->json([ 'signups' => $signups ]);
       
    }

    public function create()
    {
       
        $request = request();
        
        $course_id=(int)$request->course;  
        $course=Course::findOrFail($course_id);

        $user=$this->currentUser();

        //可否線上報名
        $net_signup=true;
        $canSignup=$this->signupService->canSignup($course, $user, $net_signup);

        

        $course->populateViewData();
        $course->center->populateViewData();

        $signup=Signup::initialize($user->id,$course->id);
        $signup['net_signup'] = 1;

         

        //資料是否齊全
        $editUser= true;//!$this->signupService->isUserDataComplete($user);
        if($editUser) $user->profile;
       
        
        return response()->json([
            'course' => $course,
            'user' => $user,
            'signup' => $signup,
            'editUser' => $editUser
        ]);
       




        

         
            
               
                
            


        //  $course= $this->courses->findOrFail($course_id);

         
        //  $center=$course->center;
        //  $center->contactInfo=$center->contactInfo();
        //  $center->contactInfo->addressA=$center->contactInfo->addressA();
        //  foreach ($course->classTimes as $classTime) {
        //     $classTime->weekday;
        //  }
         
        //  $signup=Signup::initialize($user_id,$course_id);

        //  $discounts=$this->discounts->activeDiscounts()->get();

        //  return response()
        //     ->json([
        //         'discounts' => $discounts,
        //         'signup' => $signup,
        //         'course' => $course
        //     ]);
         
    }
    
    public function store(OnlineSignupRequest $request)
    {
        $user=$this->currentUser();
        $updated_by=$user->id;
       

        if($request->hasUser())
        {
            $userValues=$request->getUserValues($updated_by);       
            $profileValues=$request->getProfileValues($updated_by);

            $user= $this->signupService->updateUser($userValues,$profileValues, $user);
            
        }

        //課程是否可以線上報名

        $values=$request->getSignupValues($updated_by);

        $course_id=$values['course_id'];
        $course=Course::findOrFail($course_id);

        $net_signup=1;
        // if(!$course->canSignup($net_signup))
        // {
        //    return $this->storeBackUpSignup($values);
        // }

        
        //  $errMsg=$this->check($course, $user, $discount);
        //  if(!empty($errMsg))
        //  {
        //        return   response()->json(['msg' => $errMsg ]  ,  422);
        //  }

        $values['date']=Carbon::today();

        $signup=new Signup($values);
        
        $signup=$this->signupService->store($course,$user,$signup);
         
        return response()->json($signup);
            
    }
    
    // public function show($id)
    // {     
    //     $current_user=request()->user();

    //     $signup=Signup::with('course','user.profile')->findOrFail($id);
    //     if(!$signup->canViewBy($current_user)){
    //         return   response()->json(['msg' => '權限不足' ]  ,  401);
    //     }
    //     $signup->points=$signup->formattedPoints();
    //     $signup->canEdit=$signup->canEditBy($current_user);
    //     $signup->canDelete=$signup->canDeleteBy($current_user);
    //     $signup->hasRefund=$signup->hasRefund();

    //     return response()->json([ 'signup' => $signup ]);
       
    // }
    public function destroy($id)
    {
        $signup=Signup::findOrFail($id); 
        $current_user=$this->currentUser();

        $this->signupService->delete($signup, $current_user);

        return response()->json([ 'deleted' => true ]);
               
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
    

   
    
}
