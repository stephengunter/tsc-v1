<?php

namespace App\Http\Controllers\Signups;

use App\Http\Controllers\BaseController;

use Illuminate\Http\Request;
use App\Http\Requests\Signups\SignupRequest;
use App\Http\Requests\Signups\SignupUserRequest;


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

class SignupsController extends BaseController
{
    protected $key='signups';
    
    public function __construct(SignupService $signupService)                       
    {
        $this->signupService=$signupService;

    }
    
    private function seed()
    {
        $course_id=2;
       
        

        $users=User::all();
        foreach($users as $user){
            $course=Course::findOrFail($course_id);
            $signup=new Signup([
                'course_id' => 2,
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

    public function index()
    {
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

           

        return response() ->json([ 'model' => $signupList,
                                    'summary' => $summary,
                                    'course' => $course
                                ]);
       
    }

    private function createByUser(User $user)
    {
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
            return $this->createByUser($user);
        }
    
        

        $course=null;
        if($course_id){
            $course= Course::findOrFail($course_id);
            $net_signup=false;
            $course->canPay=$course->canSignup($net_signup);
        }

        $courseOptions=[];
        if($course){
            array_push($courseOptions,  $course->toOption());
        }
        
        
        $userOptions =[];
        $user=null;
        if($user_id > 0){
            $user= User::with('profile')->findOrFail($user_id);
          
        }else{
            $userOptions = $this->getUserOptions($user);
        }       

       
        

        $signup=Signup::initialize($user_id,$course_id);
        $signup['net_signup'] = 0;

        $bill=Bill::init();
        $bill['total']=$course->tuition;


       
        $tuition=Tuition::initialize();

        $payways=$this->signupService->getPayways();

        return response()
        ->json([
            'courseOptions' => $courseOptions,
           
            'userOptions' => $userOptions,
            
            'course' => $course,
            'user' => $user,

            'signup' => $signup,
            'bill' => $bill,
            'tuition' => $tuition,
            'payways' => $payways


        ]);
         
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
        $current_user=$this->currentUser();        
        $updated_by=$current_user->id;

        $isPay=$request->isPay();

        $values=$request->getValues($updated_by);

        $net_signup=0;
        $values['net_signup']=$net_signup;

        $values['cost']=0;  //不代收材料費

        $course_id=$values['course_id'];
        $course=Course::findOrFail($course_id);

        // if(!$course->canSignup($net_signup))
        // {
        //    return $this->storeBackUpSignup($values);
        // }

        $user_id=$values['user_id'];
        $user=User::findOrFail($user_id);
        
        $canSignup=$this->signupService->canSignup($course, $user, $net_signup);

        $tuition=null;
      
        if($isPay){
            $tuitionValues=$request->getTuitionValues($updated_by); 
            $tuitionValues['date']=$values['date'];
            $tuition=new Tuition($tuitionValues);          
        }else{
            $values['discount_id'] = 0;
        } 

        $signup=new Signup($values);

        $signup=$this->signupService->store($course,$user,$signup,$tuition);

        

        

        return response()->json($signup);

            
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
        $signup=Signup::with('course','user.profile')->findOrFail($id);
        if($signup->parent){
            $signup=Signup::with('course','user.profile')->findOrFail($signup->parent);
           
        }

        $signup->subCourses=$signup->subSignupCourses();
        
        
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

    private function  getUserOptions($user)
    {
        $userOptions=[];
    
        if($user){           
            $item=$user->profile->toOption();             
            array_push($userOptions,  $item);
        }else
        {
          
            $userOptions=$this->signupService->userOptions();
            
        }

        return $userOptions;
    }

   
    
}
