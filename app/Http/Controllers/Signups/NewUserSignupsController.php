<?php

namespace App\Http\Controllers\Signups;

use App\Http\Controllers\BaseController;

use Illuminate\Http\Request;
use App\Http\Requests\Signups\NewUserSignupRequest;

use App\Repositories\Courses;
use App\Repositories\Discounts;
use App\Repositories\Signups;
use App\Repositories\Users;
use App\Repositories\Terms;
use App\Repositories\Centers;
use DB;

use App\Signup;
use App\User;
use App\Course;

use App\Events\UserCreated;

use App\Support\Helper;

class NewUserSignupsController extends BaseController
{
    protected $key='signups';
    public function __construct(Courses $courses, Discounts $discounts,
                                Terms $terms , Centers $centers, 
                                  Signups $signups, Users $users) 
                               
    {
       

		 $this->courses=$courses;
         $this->discounts=$discounts;
         $this->terms=$terms;
         $this->centers=$centers;
         $this->signups=$signups;
         $this->users=$users;

         

	}

    public function create()
    {
        
        if(!request()->ajax()){
            $menus=$this->menus($this->key); 
            return view('signups.new user')->with(['menus' => $menus]);
        }  

        $with_password=true;
        $user= $this->users->initialize($with_password);
        $signup=Signup::initialize();

       

        return response()
            ->json([
                'user' => $user,
                'signup' => $signup               
            ]);
         
    }
   
    
    public function store(NewUserSignupRequest $request)
    {  
         $current_user=$this->checkAdmin->getAdmin();
         $updated_by=$current_user->id;
         $removed=false;

         $values=$request->getValues($updated_by);
         $date=$values['date'];
         $course_id=$values['course_id'];
         $discount_id=(int)$values['discount_id'];

         $course=$this->courses->findOrFail($course_id);
         if(!$course->canSignup()){
              $errMsg= ['此課程目前無法報名'] ;
              return   response()->json(['signup.course_id' => $errMsg ]  ,  422);
         }

         $discount=null;
         if($discount_id > 0){
            $discount=$this->discounts->findOrFail($discount_id);           
         }
         

         $userValues=$request->getUserValues($updated_by,$removed);
         $profileValues=$request->getProfileValues($updated_by);
         $userValues['name']=$profileValues['fullname'];
         
         $userRepository=$this->users;
         $signupRepository=$this->signups;
         $signup= DB::transaction(function() 
         use($userRepository,$signupRepository,$userValues,$profileValues,
                $course, $discount, $updated_by, $date ) {

              $user=$userRepository->store($userValues, $profileValues);
              $user_id=$user->id;
              $signup=$signupRepository->store($course,$discount,$user_id,$updated_by,$date);
              
              return $signup;
         });

         return response()->json($signup);

         
         // dispatch(new SendEmailConfirmationMail($user));

         //event(new UserCreated($user));
         
         
            
    }
    
    private function check($course, $user, $discount)
    {
        if($course){
            if(!$course->canSignup()){
              return   '此課程目前無法報名' ;
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
   
    
}
