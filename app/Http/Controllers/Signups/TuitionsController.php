<?php

namespace App\Http\Controllers\Signups;

use App\Http\Controllers\BaseController;

use Illuminate\Http\Request;
use App\Http\Requests\Signups\TuitionRequest;

use App\Repositories\Tuitions;
use App\Repositories\Signups;
use App\Repositories\Payways;

use App\Tuition;
use App\Signup;
use App\Account;
use App\Support\Helper;
use App\Http\Middleware\CheckAdmin;

use App\Events\TuitionChanged;

class TuitionsController extends BaseController
{
    public function __construct(Signups $signups, Payways $payways,
                             Tuitions $tuitions, CheckAdmin $checkAdmin)                         
    {
         $exceptAdmin=[];
         $allowVisitors=[];
		 $this->setMiddleware( $exceptAdmin, $allowVisitors);

         $this->signups=$signups;
         $this->payways=$payways;
         $this->tuitions=$tuitions;

         $this->setCheckAdmin($checkAdmin);

	}
    public function index()
    {
         $request = request();
         $signup_id=(int)$request->signup; 

         if(!$signup_id)  abort(404);

         $signup=$this->signups->findOrFail($signup_id);

         $tuitionList=$this->tuitions->getBySignup($signup_id)->filterPaginateOrder();
        
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

         if(!$signup_id)  abort(404);

         $signup=Signup::with('course','user')->findOrFail($signup_id);

         $current_user=$this->checkAdmin->getAdmin();
         if(!$signup->canEditBy($current_user)){
            return   response()->json(['msg' => '權限不足' ]  ,  401);      
         }

         $payOptions=$this->payways->getAll();
         

         $tuition=Tuition::initialize($signup);

         return response()
            ->json([
                'signup' => $signup,
                'tuition' => $tuition,
                'payOptions' => $payOptions
            ]);
         
    }
    public function show($id)
    {
        $tuition=$this->tuitions->findOrFail($id);  
        $current_user=$this->checkAdmin->getAdmin();
        
        if(!$tuition->canViewBy($current_user)){
            return   response()->json(['msg' => '權限不足' ]  ,  401);    
        }  
         $tuition->textPayBy=$this->payways->textPayBy($tuition->pay_by);
         $tuition->canEdit=$tuition->canEditBy($current_user);
         $tuition->canDelete=$tuition->canDeleteBy($current_user);

         return response()
                ->json([
                    'tuition' => $tuition
                ]);
       
    }
    public function store(TuitionRequest $request)
    {
        $current_user=$this->checkAdmin->getAdmin();
        $removed=false;
        $updated_by=$current_user->id;
        $values=$request->getValues($updated_by,$removed);

        $signup_id=$values['signup_id']; 
        $signup=$this->signups->findOrFail($signup_id);
        if(!$signup->canEditBy($current_user)){
            return   response()->json(['msg' => '權限不足' ]  ,  401);    
        }

        $pay_by=$values['pay_by'];
       
        if(!$this->payways->payByBank($pay_by)){
             $values=array_except($values, ['bank_branch','account_owner', 'account_number']);             
        }

        if(array_key_exists('refund', $values)) {
             $values['refund']= false;
        }else{
           $values=array_add($values, 'refund', false);
        }
       

        $tuition=Tuition::create($values);

        event(new TuitionChanged($signup));
       
        return response()->json($tuition);
      
    }
    public function edit($id)
    {
        $tuition=$this->tuitions->findOrFail($id);  
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
        $tuition=$this->tuitions->findOrFail($id);  
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

        $signup_id=$tuition->signup_id;

        event(new TuitionChanged($tuition->signup));
       
        return response()->json($tuition);
      
    }
    public function destroy($id)
    {
        $tuition=$this->tuitions->findOrFail($id);  
        $signup_id=$tuition->signup_id;

        $current_user=$this->checkAdmin->getAdmin();        
        if(!$tuition->canDeleteBy($current_user)){
            return   response()->json(['msg' => '權限不足' ]  ,  401);    
        }        
      

        $tuition->delete();

        event(new TuitionChanged($tuition->signup));

        return response()
                ->json([
                    'deleted' => true
                ]);
    }


}
