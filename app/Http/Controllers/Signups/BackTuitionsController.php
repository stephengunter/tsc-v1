<?php

namespace App\Http\Controllers\Signups;

use App\Http\Controllers\BaseController;

use Illuminate\Http\Request;
use App\Http\Requests\Signups\TuitionRequest;

use App\Repositories\BackTuitions;
use App\Repositories\Signups;
use App\Repositories\Payways;
use App\Repositories\Refunds;

use App\Tuition;
use App\Refund;
use App\Signup;
use App\Account;

use App\Support\Helper;
use App\Http\Middleware\CheckAdmin;

use App\Events\BackTuitionChanged;

class BackTuitionsController extends BaseController
{
    public function __construct(Refunds $refunds, Signups $signups, Payways $payways, 
                             BackTuitions $backTuitions, CheckAdmin $checkAdmin)                         
    {
         $exceptAdmin=[];
         $allowVisitors=[];
		 $this->setMiddleware( $exceptAdmin, $allowVisitors);

		 $this->refunds=$refunds;
         $this->signups=$signups;
         $this->payways=$payways;
         $this->backTuitions=$backTuitions;

         $this->setCheckAdmin($checkAdmin);

	}
    public function index()
    {
         $request = request();
         $signup_id=(int)$request->signup; 

         if(!$signup_id)  abort(404);

         $signup=$this->signups->findOrFail($signup_id);

         $tuitionList=$this->backTuitions->getBySignup($signup_id)->filterPaginateOrder();
        
         foreach ($tuitionList as $tuition) {
            $tuition->signup=$signup;
            $tuition->textPayBy=$this->payways->textPayBy($tuition->pay_by);
              
         }

          return response() ->json(['model' => $tuitionList]); 
       
    }
   

    public function create()
    {
         $request = request();
         $signup_id=(int)$request->signup; 
         $signup=Signup::with('course','user')->findOrFail($signup_id);

         if(!$signup_id)  abort(404);
         $refund=$this->refunds->find($signup_id);
         if(!$refund) abort(404);

         $current_user=$this->checkAdmin->getAdmin();
         if(!$refund->canEditBy($current_user)){
            return   response()->json(['msg' => '權限不足' ]  ,  401);      
         }
       
         $tuition=Tuition::initialize($signup,$refund);
         
         $payOptions=$this->payways->getAll();
         return response()
            ->json([
                'signup' => $signup,
                'tuition' => $tuition,
                'payOptions' => $payOptions
            ]);
         
    }
    public function store(TuitionRequest $request)
    {
        $current_user=$this->checkAdmin->getAdmin();
        $removed=false;
        $updated_by=$current_user->id;
        $values=$request->getValues($updated_by,$removed);

        $signup_id=$values['signup_id']; 
        $refund=$this->refunds->find($signup_id);
        if(!$refund) abort(404);
        if(!$refund->canEditBy($current_user)){
            return   response()->json(['msg' => '權限不足' ]  ,  401);    
        }

        $pay_by=$values['pay_by'];
       
        if(!$this->payways->payByBank($pay_by)){
             $values=array_except($values, ['bank_branch','account_owner', 'account_number']);             
        }

        if(array_key_exists('refund', $values)) {
             $values['refund']= true;
        }else{
           $values=array_add($values, 'refund', true);
        }
       

        $tuition=Tuition::create($values);
        
        event(new BackTuitionChanged($refund));
       
        return response()->json($tuition);
      
    }
    public function edit($id)
    {
        $tuition=$this->backTuitions->findOrFail($id);  
        $current_user=$this->checkAdmin->getAdmin();
        
        if(!$tuition->canEditBy($current_user)){
            return   response()->json(['msg' => '權限不足' ]  ,  401);    
        }

        $tuition->canDelete=$tuition->canDeleteBy($current_user);   

        $signup_id=$tuition->signup_id;
        $signup=Signup::with('course','user')->findOrFail($signup_id);

        $payOptions=$this->payways->getAll();
       

         return response()
            ->json([
                'signup' => $signup,
                'tuition' => $tuition,
                'payOptions' => $payOptions
               
            ]);      
    }
    public function update(TuitionRequest $request, $id)
    {
        $tuition=$this->backTuitions->findOrFail($id);  
        $current_user=$this->checkAdmin->getAdmin();
        
        if(!$tuition->canEditBy($current_user)){
            return   response()->json(['msg' => '權限不足' ]  ,  401);    
        }  
       
        $removed=false;
        $updated_by=$current_user->id;
        $values=$request->getValues($updated_by,$removed);
        
        $pay_by=$values['pay_by'];
        if(!$this->payways->payByBank($pay_by)){
             $values=array_except($values, ['bank_branch','account_owner', 'account_number']);
        }
        
        $tuition->update($values);

        event(new BackTuitionChanged($refund));
       
        return response()->json($tuition);
      
    }
    public function destroy($id)
    {
        $tuition=$this->backTuitions->findOrFail($id);  
        $signup_id=$tuition->signup_id;
        

        $current_user=$this->checkAdmin->getAdmin();        
        if(!$tuition->canDeleteBy($current_user)){
            return   response()->json(['msg' => '權限不足' ]  ,  401);    
        }        

        $tuition->delete();

        $refund=$this->refunds->find($signup_id);
        if($refund){
            event(new BackTuitionChanged($refund));
        }

        return response()
                ->json([
                    'deleted' => true
                ]);
    }


}
