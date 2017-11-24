<?php

namespace App\Http\Controllers\Signups;

use App\Http\Controllers\BaseController;

use Illuminate\Http\Request;
use App\Http\Requests\Signups\NewUserSignupRequest;

use App\Services\User\UserService;
use App\Services\Signup\SignupService;

use App\Signup;
use App\User;
use App\Profile;
use App\Course;
use App\Tuition;

use App\Events\UserCreated;

use App\Support\Helper;

use Exception;

class NewUserSignupsController extends BaseController
{
    protected $key='signups';
    
    public function __construct(SignupService $signupService, UserService $userService) 
    {
        $this->userService=$userService;
        $this->signupService=$signupService;
	}

    public function create()
    {
        
        if(!request()->ajax()){
            $menus=$this->menus($this->key); 
            return view('signups.new user')->with(['menus' => $menus]);
        }  

        $with_password=true;
        $user= User::initialize($with_password);
        $signup=Signup::initialize();
        $signup['net_signup'] = 0;

        $tuition=Tuition::initialize();
        
        $payways=$this->signupService->getPayways();

        return response()
            ->json([
                'user' => $user,
                'signup' => $signup,
                'tuition' => $tuition,
                'payways' => $payways               
            ]);
         
    }
   
    
    public function store(NewUserSignupRequest $request)
    {  
        
        $current_user=$this->currentUser();        
        $updated_by=$current_user->id;

        $isPay=$request->isPay();

        $values=$request->getValues($updated_by);

        $course_id=$values['course_id'];
        $course=Course::findOrFail($course_id);

        $userValues=$request->getUserValues($updated_by);
        $profileValues=$request->getProfileValues($updated_by);
        $userValues['name']=$profileValues['fullname'];

        $user=new User($userValues);
        $profile=new Profile($profileValues);

        $user=$this->userService->store($user,$profile);

        if(!$user) throw new Exception();

        try {

            $canSignup=$this->signupService->canSignup($course, $user);

            $tuition=null;
            
            if($isPay){
                $tuitionValues=$request->getTuitionValues($updated_by); 
                $tuitionValues['date']=$values['date'];
                $tuition=new Tuition($tuitionValues);
            }else{
                $values['discount_id'] = 0;
            }
            
            $signup=new Signup($values);
           
            $signup->user_id=$user->id;

            
            $signup=$this->signupService->store($course,$user,$signup,$tuition);

            return response()->json($signup);
        }
        catch (Exception $err) {
            $user->delete();

            throw $err;
        }



        

        dd('done');


        

        

        $tuitionValues=null;
      
        if($isPay){
            $tuitionValues=$request->getTuitionValues($updated_by);           
        }else{
            $values['discount_id'] = 0;
        } 

        $signup=new Signup($values);
        
        if($signup->discount_id){
            $discount=$this->discounts->countTuition($course,$discount_id);
            if($discount->tuition!=$signup->tuition){  
                return $this->requestError('signup.tuition','課程費用錯誤');                
            }

            $signup->discount=$discount->name;
            $signup->points=$discount->points;
        }

        $signup=$this->signupService->storeNewUserSignup($course,$user,$profile,$signup);

        //event(new SignupCreated($signup));

        return response()->json($signup);




        dd('done');


        $course_id=$values['course_id'];
        $course=$this->courses->findOrFail($course_id);

        
        
        // if(!$course->canSignup()){
        //     $errMsg= ['此課程目前無法報名'] ;
        //     return   response()->json(['signup.course_id' => $errMsg ]  ,  422);
        // }

        $discount=null;
        $discount_id=(int)$values['discount_id'];
        if($discount_id > 0){
            $discount=$this->discounts->findOrFail($discount_id);
        
        }

        $userValues=$request->getUserValues($updated_by);
        $profileValues=$request->getProfileValues($updated_by);
        $userValues['name']=$profileValues['fullname'];

        $user=$this->users->store($userValues, $profileValues);

        $signup=new Signup($values);
        // if($discount_id){
        //     $discount=$this->discounts->countTuition($course,$discount_id);
           
        //     if($discount->tuition!=$values['tuition']){                    
               
        //         return $this->requestError('signup.tuition','課程費用錯誤');
               
        //     }
            
        //     $signup->discount=$discount->name;
        //     $signup->points=$discount->points;    

        // }

        $user->signups()->save($signup);
        
        if($isPay){
            $tuition=new Tuition($tuitionValues);
            $tuition->date=$signup->date;
            $signup->tuitions()->save($tuition);
            
        }
        
        
        

        return response()->json($signup);

         
         // dispatch(new SendEmailConfirmationMail($user));

         //event(new UserCreated($user));
         
         
            
    }

    
    
   
    
    // private function  getCourseOptions($course)
    // {
    //      $courseOptions=[];
         
    //      if($course){
    //          $item=$course->toOption();
    //          array_push($courseOptions,  $item);
    //      }else
    //      {
    //          $courseList=$this->courses->canSignupCourses();
    //          if($courseList->count()){
    //              $courseOptions=$this->courses->optionsConverting($courseList->get());
    //          }
            
    //      }

    //      return $courseOptions;
    // }
   
    
}
