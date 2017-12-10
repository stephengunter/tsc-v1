<?php

namespace App\Http\Controllers\Signups;

use App\Http\Controllers\BaseController;

use Illuminate\Http\Request;
use App\Http\Requests\Signups\SignupRequest;
use App\Http\Requests\Signups\SignupUserRequest;


use App\Services\Signup\SignupService;
use App\Services\Signup\DiscountService;

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
use DB;

use App\Exceptions\RequestError;

class SignupsController extends BaseController
{
    protected $key='signups';
    
    public function __construct(SignupService $signupService,DiscountService $discountService)                       
    {
        $this->signupService=$signupService;
        $this->discountService=$discountService;

    }
    
    private function seed()
    {
        $course_id=2;

        $users=User::all();
        foreach($users as $user){
            $course=Course::findOrFail($course_id);
            $signup=new Signup([
                'course_id' => $course_id,
                'user_id' => $user->id,
                'net_signup' =>  0,
                'parent' => 0,
                'status' => 0,
                'date' => '2017-11-21'
            ]);
            
           
            try {
                $canSignup=$this->signupService->canSignup($course, $user , $signup->net_signup);
              
            }
            catch (Exception $err) {
               
                continue;
            }    

           
    
            $tuition=new Tuition([
                'date' => $signup->date,
                'pay_by' => 1,
                'amount' => $course->tuition,

            ]);    

            
    
            $signup=$this->signupService->store($course,$user,$signup,$tuition);

           
    
        }  //end for
    }

    public function indexOptions()
    {
        $termOptions=$this->signupService->termOptions();
        $centerOptions=$this->signupService->centerOptions();

        return response()
            ->json([
                'termOptions' => $termOptions,
                'centerOptions' => $centerOptions,
            ]);

    }

    private function checkValidStatus(int $status)
    {
        if( $status >= -1  && $status <=1 ) return $status;
        return 0;
    }

    private function test()
    {
        $course=Course::findOrFail(1);
        $center_id=$course->center_id;
        $term=$course->term;

        $discountOptions=$this->discountService->getDiscountOptions($center_id,  $term);


        dd($discountOptions);
    }

    public function index()
    {
        $this->test();

        $request = request();
          
        if(!$request->ajax()){
            $menus=$this->menus($this->key);   
            return view('signups.index')
                    ->with(['menus' => $menus]);
        }  
        
       
        $course_id=(int)$request->course; 
        $user_id=(int)$request->user;
        $status=(int)$request->status;
        $status=$this->checkValidStatus($status);

        $signupList=null;
        $summary=null;
        $course=null;

        if($course_id){
            $course=Course::with('status')->findOrFail($course_id);
            $net_signup=false;
            $course->canSignup=$course->canSignup($net_signup);

            $signupList= $this->signupService->getByCourseId($course_id);

            $summary=$this->signupService->getSummary($course_id);
            
        }else{
            $signupList= $this->signupService->getByUserId($user_id);

            $summary=$this->signupService->getSummaryByUser($user_id);
        }

        $signupList=$signupList->where('status',$status)
                                ->with(['course','user.profile'])
                                ->filterPaginateOrder();

        foreach($signupList as $signup){
            $signup->populateViewData();
        }

           

        return response() ->json([ 'model' => $signupList,
                                    'summary' => $summary,
                                    'course' => $course
                                ]);
       
    }

    private function createByUser(User $user,int $course_id=0)
    {
        if($course_id){
            $course= Course::findOrFail($course_id);
            $isNetSignup=true;

            $errMsg=$course->canSignupBy($user,$isNetSignup);

            if($errMsg) $course->error=$errMsg;

           

            $course->populateViewData();

            return response()->json([
                'course' => $course
            ]);
        }
        $signups=[];
        $bill=Bill::init();
        $tuition=Tuition::initialize();

        $payways=$this->signupService->getPayways();

        return response()->json([
                            
                            'signups' => $signups,
                            'bill' => $bill,
                            'tuition' => $tuition,
                            'payways' => $payways

                        ]);
    }

    private function createByCourse(Course $course)
    {
        if($course->removed) abort(404);
        if(!$course->active) abort(404);

        
        $course->populateViewData();

        $userOptions=$this->signupService->userOptions();
        $user_id=0;
        $signup=Signup::initialize($user_id,$course);
        $signup['net_signup'] = 0;

        $bill=Bill::init();
        $bill['total']=$course->tuition;
       
        $tuition=Tuition::initialize();

       
        $payways=$this->signupService->getPayways();

        return response()
        ->json([
            
            'course' => $course,
            'signup' => $signup,
            'bill' => $bill,
            'tuition' => $tuition,
            'userOptions' => $userOptions,
            'payways' => $payways

        ]);
    }

    public function create()
    {
        
        $request = request();
        $course_id=(int)$request->course; 
        $user_id=(int)$request->user; 

        if(!$request->ajax()){
           

            $menus=$this->menus($this->key);            
            return view('signups.create')
                    ->with([
                        'menus' => $menus,
                        'user_id' => $user_id,
                        'course_id' => $course_id
                    ]);
        }  

        if($user_id){
            $user=User::findOrFail($user_id);
            return $this->createByUser($user,$course_id);
        }
        
        $course= Course::findOrFail($course_id);
        return $this->createByCourse($course);
        
         
    }
    
    private function storeBackUpSignup($values)
    {
        $course_id=$values['course_id'];
        $user_id=$values['user_id'];
        $isUserHasSignuped=$this->signupService->isUserHasSignuped($course_id, $user_id);

        if($isUserHasSignuped){
            throw new RequestError('signup.user_id','此學員已報名過此課程了');
        }

        $values['discount_id']=0;
        $values['discount']='';
        $values['points']=0;
        $values['cost']=0;
        $values['tuition']=0;
        $signup=Signup::create($values);

      //event(new SignupCreated($signup));

        return response()->json($signup);
    }
    
    public function store(SignupRequest $request)
    {  
        //必須繳費,單一課程
        $current_user=$this->currentUser();        
        $updated_by=$current_user->id;
        

        $values=$request->getValues($updated_by);

        $net_signup=0;
        $course=Course::findOrFail($values['course_id']);
        $user=User::findOrFail($values['user_id']);

        $this->signupService->canSignup($course, $user, $net_signup);

        $values['net_signup']=$net_signup;
        $values['cost']=0;  //不代收材料費
        $signup=new Signup($values);

        if(!$request->isPay()) {
            $signup->save();
            return response()->json($signup);
        } 


        

        $billValues=$request->getBillValues($updated_by);
        $bill=new Bill($billValues);

        $tuitionValues=$request->getTuitionValues($updated_by); 
        $tuition=new Tuition($tuitionValues);

        $signups=[$signup];
        
        $bill= $this->signupService->storeBillAndSignups($bill,$tuition,$signups);

        return response()->json($bill);

            
    }
    public function edit($id)
    {
        $signup=Signup::findOrFail($id);  
        if($signup->parent){
            $signup=Signup::findOrFail($signup->parent);           
        }

        $signup->subCourses=$signup->subSignupCourses();

        $current_user=$this->currentUser();
        if(!$signup->canEditBy($current_user)){
            return  $this->unauthorized();  
        }  

        $course_id=$signup->course_id;
        $course= Course::find($course_id);
         
        $user_id=$signup->user_id;
        
        $user= User::with('profile')->find($user_id);
  
        

         return response()
            ->json([
               
                'signup' => $signup,
                'course' => $course,
                'user' => $user

            ]);
    }
    public function update(SignupRequest $request, $id)
    {
        $signup=Signup::findOrFail($id);  
        $current_user=$this->currentUser();
        if(!$signup->canEditBy($current_user)){
            return  $this->unauthorized();  
        } 

        $updated_by=$current_user->id;
        $values=$request->getValues($updated_by);

        $signup=$this->signupService->update($signup,$values);

        // $course_id=$values['course_id'];
        // $course=$this->courses->findOrFail($course_id);
        // if($course->groupAndParent())
        // {
        //     $sub_course_ids=$values['sub_courses'];
        
        //     $signup=$this->signups->updateGroupSignup($signup,$discount,$user_id,$updated_by,$date,
        //                                             $net_signup , $sub_course_ids);

        
        // }else{
        //     $signup=$this->signups->update($signup,$discount,
        //             $user_id,$updated_by,$date,$net_signup);
        // }
         
         
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
        $with=['course','user.profile','bill'];
        $signup=Signup::with($with)->findOrFail($id);
        
        
        
        if(!$signup->canViewBy($current_user)){
            return  $this->unauthorized(); 
        }
       
        
        $signup->canEdit=$signup->canEditBy($current_user);
        $signup->canDelete=$signup->canDeleteBy($current_user);        
        $signup->hasRefund=$signup->hasRefund();

        $signup->populateViewData();

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
    public function updateUser(SignupUserRequest $request)
    {
        $current_user=$this->currentUser();
        $updated_by=$current_user->id;
        $removed=false;
        $userValues=$request->getUserValues($updated_by,$removed);       
        $profileValues=$request->getProfileValues($updated_by);
       
        $user_id=$userValues['id'];
        $user=User::findOrFail($user_id);

        $user= $this->users->updateUserAndProfile($userValues,$profileValues, $user);
        
        return response()->json($user);
        
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
        $options=$this->signupService->statusOptions();
        return response()
                ->json([
                    'options' => $options
                ]);
    }

    

    
    
    private function  getCourseOptions($course)
    {
        $courseOptions=[];
        
        if($course){
            $item=$course->toOption();
            array_push($courseOptions,  $item);
        }else
        {
            return $this->signupService->getCourseOptions();
            
        
        }

        return $courseOptions;
    }

   

   
    
}
