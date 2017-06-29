<?php

namespace App\Http\Controllers\Signups;

use App\Http\Controllers\BaseController;

use Illuminate\Http\Request;

use App\Http\Requests\Signups\RefundRequest;
use App\Repositories\Refunds;
use App\Repositories\Courses;
use App\Repositories\Signups;
use App\Repositories\Users;
use App\Repositories\Payways;
use App\Repositories\Terms;
use App\Repositories\Centers;

use App\Signup;
use App\Refund;

use App\Support\Helper;
use App\Http\Middleware\CheckAdmin;

use App\Events\RefundChanged;

use PDF;

class RefundsController extends BaseController
{
    protected $key='refunds';
    public function __construct(Refunds $refunds,Signups $signups, Courses $courses, 
                                 Terms $terms , Centers $centers,
                                Payways $payways,Users $users, CheckAdmin $checkAdmin)                      
    {
         $exceptAdmin=[];
         $allowVisitors=[];
		 $this->setMiddleware( $exceptAdmin, $allowVisitors);

         $this->refunds=$refunds;
		 $this->courses=$courses;
         $this->signups=$signups;
         $this->payways=$payways;
         $this->users=$users;
         $this->terms=$terms;
         $this->centers=$centers;

         $this->setCheckAdmin($checkAdmin);

	}
    public function indexOptions()
    {
        $termOptions=$this->terms->options();
        $centerOptions=$this->centers->options();
        $statusOptions=$this->refunds->statusOptions();

        return response()
            ->json([
                'termOptions' => $termOptions,
                'centerOptions' => $centerOptions,
                'statusOptions' => $statusOptions,
            ]);

    }
    public function index()
    {
         $request = request();   
          
         if(!$request->ajax()){
            $menus=$this->menus($this->key);            
            return view('refunds.index')
                    ->with(['menus' => $menus]);
         } 

         $course_id=(int)$request->course;
         $status=(int)$request->status;  

         $refundList=[];
         $signup_ids=[];
         if($course_id > 0){
            $signup_ids=$this->getSignupIdsByCourse($course_id);           
            
         }else{
            $center_id=(int)$request->center;
            $term_id=(int)$request->term;  

            $course_ids=$this->courses->searchByCenter($center_id,$term_id)->pluck('id')->toArray();
           
            for($i = 0; $i < count($course_ids); ++$i) {
                $signup_ids= array_merge($signup_ids,$this->getSignupIdsByCourse($course_ids[$i]));
            }
           
         }

         $refundList=$this->refunds->getBySignupIds($signup_ids);         
         $summary=$this->refunds->getSummary($signup_ids);    

         $refundList=$refundList->where('status',$status);
         $refundList=$refundList->with('signup.course','signup.user.profile')->filterPaginateOrder();
       

          
      

         foreach ($refundList as $refund) {
              $refund->total= $refund->getTotal();
              $refund->textPayBy=$this->payways->textPayBy($refund->pay_by);
         }
       
       

           return response()
            ->json([
                'model' => $refundList,
                'summary' => $summary
            ]);
       
    }

    public function create()
    {
        $request = request();
        if(!$request->ajax()){
            $menus=$this->menus($this->key);            
            return view('refunds.create')
                    ->with(['menus' => $menus]);
         }  

        $signup_id=(int)$request->signup; 

        $signup=Signup::with('course','user.profile')->findOrFail($signup_id);
        $refund=Refund::initialize($signup);

        $payOptions=$this->payways->bankOnly();
        
        return response()
            ->json([
                'signup' => $signup,
                'refund' => $refund,
                'payOptions' => $payOptions
            ]);
        
    }
    public function store(RefundRequest $request)
    {
         $current_user=$this->currentUser();
         $removed=false;
         $updated_by=$current_user->id;
         $values=$request->getValues($updated_by, $removed);

         $signup_id=$values['signup_id']; 
         $signup=Signup::findOrFail($signup_id);
         if(!$signup->canEditBy($current_user)){
             return  $this->unauthorized();   
         }



         $refund=$this->refunds->store($values,$signup);

         event(new RefundChanged($signup));

         return response()->json($refund);
            
    }
    public function show($id)
    {
        if(!request()->ajax()){
            $menus=$this->menus($this->key);            
            return view('refunds.details')
                    ->with([ 'menus' => $menus,
                              'id' => $id     
                        ]);
         }  

        $current_user=$this->currentUser();

        $refund=$this->refunds->getById($id);
        if(!$refund){
            return response()
                ->json([
                    'refund' => [
                        'signup_id' => 0
                    ]
                ]);
        }

        if(!$refund->canViewBy($current_user)){
            return  $this->unauthorized();
        }

         $refund->canEdit=$refund->canEditBy($current_user);
         $refund->canDelete=$refund->canDeleteBy($current_user);
         $refund->total=$refund->getTotal();
         $refund->textPayBy=$this->payways->textPayBy($refund->pay_by);

         return response()
                ->json([
                    'refund' => $refund
                ]);
       
    }
    public function edit($id)
    {
        $current_user=$this->currentUser();

        $refund=$this->refunds->findOrFail($id);
        if(!$refund->canEditBy($current_user)){
             return  $this->unauthorized();
        }

        $signup_id=$refund->signup_id;

        $signup=Signup::with('course','user.profile')->findOrFail($signup_id);
        $payOptions=$this->payways->getAll();
        $statusOptions=$this->refunds->statusOptions();
        
        return response()
            ->json([
                'signup' => $signup,
                'refund' => $refund,
                'payOptions' => $payOptions,
                'statusOptions' => $statusOptions
            ]);

    }
    public function update(RefundRequest $request, $id)
    {
        $current_user=$this->currentUser();

        $refund=$this->refunds->findOrFail($id);
        if(!$refund->canEditBy($current_user)){
            return  $this->unauthorized();
        }
        $removed=false;
        $updated_by=$current_user->id;
        $values=$request->getValues($updated_by, $removed);

        $refund->update($values);
        
        event(new RefundChanged($refund->signup));

        return response()->json($refund);
    }
    public function destroy($id)
    {
        $refund=$this->refunds->findOrFail($id);
        $current_user=$this->currentUser();

        if(!$refund->canDeleteBy($current_user)){
            return  $this->unauthorized(); 
        } 

        $updated_by=$current_user->id;
        $values=[
            'removed' => 1,
            'updated_by' => $updated_by
        ];
        
        $refund->update($values);   

        event(new RefundChanged($refund->signup));

        return response()
                ->json([
                    'deleted' => true
                ]);
    }

    public function print($id)
    {
        $current_user=$this->currentUser();
     
        $signup=Signup::with('course.center','user.profile')->findOrFail($id);
        if(!$signup->hasRefund()) abort(404);
       
        $signup->refund->total=Helper::formatMoney($signup->refund->getTotal());
        $signup->refund->tuition=Helper::formatMoney($signup->refund->tuition);
        $signup->refund->charge=Helper::formatMoney($signup->refund->charge);
        $signup->refund->points=Helper::formatMoney($signup->refund->points);
       
        $title=$signup->course->name . ' 退費申請 ' . $signup->user->profile->fullname; 
        
        $pdf = PDF::loadView('refunds.form', [
                                    'title' => $title,
                                 'signup' => $signup
                            ]);

                           
        return $pdf->stream();
    }

    public function statusOptions()
    {
        $options=$this->refunds->statusOptions();
        return response()
                ->json([
                    'options' => $options
                ]);
    }

    private function getSignupIdsByCourse($course_id)
    {
        return $this->signups->getByCourse($course_id)->pluck('id')->toArray();
    }
}
