<?php

namespace App\Http\Controllers\Api;

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


use App\Http\Requests\Course\SignupRequest;

use App\Support\Helper;
use App\Http\Middleware\CheckAdmin;

class SignupsController extends Controller
{
    protected $key='signups';
    public function __construct(Courses $courses, Discounts $discounts,
                                Terms $terms , Centers $centers, 
                                  Signups $signups, Users $users, CheckAdmin $checkAdmin) 
                               
    {
        //  $exceptAdmin=['store','show','getByUser'];
        //  $allowVisitors=[];
		//  $this->setMiddleware( $exceptAdmin, $allowVisitors);

		 $this->courses=$courses;
         $this->discounts=$discounts;
         $this->terms=$terms;
         $this->centers=$centers;
         $this->signups=$signups;
         $this->users=$users;

         $this->checkAdmin=$checkAdmin;

	}

    

    public function index()
    {
         if(!request()->ajax()){
            $menus=$this->menus($this->key);            
            return view('signups.index')
                    ->with(['menus' => $menus]);
         }  

         $request = request();

         $course_id=(int)$request->course; 
         $user_id=(int)$request->user;
         $status=(int)$request->status; 

         $summary=null;
         if($course_id > 0 ){
             $summary=$this->signups->getSummary($course_id);            
         }
         
         $signupList=$this->signups->index($course_id,$user_id,$status)->filterPaginateOrder();

           return response()
            ->json([
                'model' => $signupList,
                'summary' => $summary
            ]);
       
    }

    public function create()
    {
         $request = request();
        
         $course_id=(int)$request->course; 
         $user_id=(int)$request->user_id; 

         $course=null;
         if($course_id>0){
            $course= $this->courses->findOrFail($course_id);
         }
         $courseOptions=$this->getCourseOptions($course);
         if(empty($courseOptions)) {
             return   response()->json(['msg' => '無課程可報名' ]  ,  422);   
         }
         
         $user=null;
         if($user_id>0){
            $user= $this->users->findOrFail($user_id);
         }       
         $userOptions = $this->getUserOptions($user);

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
    
   
    
    public function store(Request $request)
    {
         $values=$request->get('signup');

         $current_user=request()->user();
         $updated_by=$current_user->id;


         $user_id=$values['user_id'];
         $user=$this->users->findOrFail($user_id);

         $course_id=$values['course_id'];
         $course=$this->courses->findOrFail($course_id);

         $discount=null;
         $discount_id=(int)$values['discount_id'];
         if($discount_id > 0){
            $discount=$this->discounts->findOrFail($discount_id);
           
         }
         $errMsg=$this->check($course, $user, $discount);
         if(!empty($errMsg))
         {
               return   response()->json(['msg' => $errMsg ]  ,  422);
         }

         $date=$values['date'];
         
         $signup=$this->signups->store($course,$discount,$user_id,$updated_by,$date);
         
         $signup->course=$course;
         return response()->json($signup);
            
    }
    public function edit($id)
    {
        $signup=$this->signups->findOrFail($id);  
        $current_user=$this->checkAdmin->getAdmin();
        if(!$signup->canEditBy($current_user)){
            return   response()->json(['msg' => '權限不足' ]  ,  401);    
        }  

        $course_id=$signup->course_id;
        $course= Course::find($course_id);
        $courseOptions=$this->getCourseOptions(null);
         
        $user_id=$signup->user_id;
        $user= User::with('profile')->find($user_id);
        $userOptions = $this->getUserOptions(null);

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
    public function update(Request $request, $id)
    {
         $signup=$this->signups->findOrFail($id);  
         $current_user=$this->checkAdmin->getAdmin();
         if(!$signup->canEditBy($current_user)){
            return   response()->json(['msg' => '權限不足' ]  ,  401);    
         } 

         $values=$request->get('signup');
         $updated_by=$current_user->id;
          

        
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

         $errMsg=$this->check($course, $user, $discount);
         if(!empty($errMsg))
         {
             return   response()->json(['msg' => $errMsg ]  ,  422);
         }

          $date=$values['date'];
         
         
         $signup=$this->signups->update($signup,$course,$discount,$user_id,$updated_by,$date);
         
         
         return response()->json($signup);
            
    }
    
    public function show($id)
    {     
        $current_user=request()->user();

        $signup=Signup::with('course','user.profile')->findOrFail($id);
        if(!$signup->canViewBy($current_user)){
            return   response()->json(['msg' => '權限不足' ]  ,  401);
        }

         $signup->canEdit=$signup->canEditBy($current_user);
         $signup->canDelete=$signup->canDeleteBy($current_user);
         $signup->hasRefund=$signup->hasRefund();

         return response()
                ->json([
                    'signup' => $signup
                ]);
       
    }
    public function destroy($id)
    {
        $signup=$this->signups->findOrFail($id); 
        $current_user=request()->user();

        if(!$signup->canDeleteBy($current_user)){
            return   response()->json(['msg' => '權限不足' ]  ,  401);    
        }    

        $this->signups->delete($id, $current_user->id);

        return response()
                ->json([
                    'deleted' => true
                ]);
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
