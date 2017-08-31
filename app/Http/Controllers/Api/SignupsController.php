<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;

use Illuminate\Http\Request;
use App\Repositories\Courses;
use App\Repositories\Discounts;
use App\Repositories\Signups;
use App\Repositories\Users;
use App\Repositories\Terms;
use App\Repositories\Centers;

use App\Signup;
use App\User;
use App\Course;

use Carbon\Carbon;
use App\Http\Requests\Course\SignupRequest;

use App\Support\Helper;

class SignupsController extends BaseController
{
    
    public function __construct(Courses $courses, Discounts $discounts,
                                Terms $terms , Centers $centers, 
                                  Signups $signups, Users $users) 
                               
    {
         $this->middleware('auth:api',['except' => ['create'] ]);
       

		 $this->courses=$courses;
         $this->discounts=$discounts;
         $this->terms=$terms;
         $this->centers=$centers;
         $this->signups=$signups;
         $this->users=$users;

	}

    

    public function index()
    {
        $user=request()->user();
        $signupList=$this->signups->getAll()       
                                ->where('user_id',$user->id)
                                ->where('status','>','-1')
                                ->orderBy('date','desc')
                                ->with(['course','user.profile'])
                                ->get();
        foreach ($signupList as $signup) {
              $signup->points=$signup->formattedPoints();    
        }                       

        return response() ->json([ 'signupList' => $signupList ]);
       
    }

    public function create()
    {
         $request = request();
        
         $course_id=(int)$request->course; 
         $user_id=(int)$request->user; 

         $course=null;
         if(!$course_id){
            abort(404);
         }
         $course= $this->courses->findOrFail($course_id);
         $center=$course->center;
         $center->contactInfo=$center->contactInfo();
         $center->contactInfo->addressA=$center->contactInfo->addressA();
         foreach ($course->classTimes as $classTime) {
            $classTime->weekday;
         }
         
         $signup=Signup::initialize($user_id,$course_id);

         $discounts=$this->discounts->activeDiscounts()->get();

         return response()
            ->json([
                'discounts' => $discounts,
                'signup' => $signup,
                'course' => $course
            ]);
         
    }
    
    public function store(Request $request)
    {
         $values=$request->get('signup');

         $user=request()->user();
         $updated_by=$user->id;
         
         $user_id=$user->id;

         $course_id=$values['course_id'];
         $course=$this->courses->findOrFail($course_id);

         $discount=null;
         $discount_id=(int)$values['discount_id'];
         if($discount_id > 0){
            $discount=$this->discounts->findOrFail($discount_id);
           
         }
        //  $errMsg=$this->check($course, $user, $discount);
        //  if(!empty($errMsg))
        //  {
        //        return   response()->json(['msg' => $errMsg ]  ,  422);
        //  }

        
         
         $signup=$this->signups->netSignup($course,$discount,$user_id,$updated_by);
         
         return response()->json($signup);
            
    }
    
    public function show($id)
    {     
        $current_user=request()->user();

        $signup=Signup::with('course','user.profile')->findOrFail($id);
        if(!$signup->canViewBy($current_user)){
            return   response()->json(['msg' => '權限不足' ]  ,  401);
        }
        $signup->points=$signup->formattedPoints();
        $signup->canEdit=$signup->canEditBy($current_user);
        $signup->canDelete=$signup->canDeleteBy($current_user);
        $signup->hasRefund=$signup->hasRefund();

        return response()->json([ 'signup' => $signup ]);
       
    }
    public function destroy($id)
    {
        $signup=$this->signups->findOrFail($id); 
        $current_user=request()->user();

        if(!$signup->canCancelBy($current_user)){
            return   response()->json(['msg' => '權限不足' ]  ,  401);    
        }    

        $this->signups->cancel($id, $current_user->id);

        return response()->json([ 'canceled' => true ]);
               
                   
               
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
