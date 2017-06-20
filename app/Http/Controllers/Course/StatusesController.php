<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

use App\Http\Requests\Course\StatusRequest;

use App\Status;
use App\Course;

use App\Support\Helper;
use App\Http\Middleware\CheckAdmin;

class StatusesController extends BaseController
{
   
    public function __construct(CheckAdmin $checkAdmin) 
    {
       
		$exceptAdmin=[];
        $allowVisitors=[];
        $this->setMiddleware( $exceptAdmin, $allowVisitors);
         
        $this->setCheckAdmin($checkAdmin);
		
	}
    
    public function show($id)
    {
        $current_user=$this->currentUser();
        $status=Status::findOrFail($id);
        if($status->hasRemoved()) {
            abort(404);
        }
        $status->canEdit=$status->canEditBy($current_user);
        $status->canDelete=$status->canDeleteBy($current_user);

        return response()->json(['status' => $status ]);   
       
    }
    public function edit($id)
    {
        $current_user=$this->currentUser();
        $status=Status::findOrFail($id);
        if($status->hasRemoved()) {
            abort(404);
        }
        if(!$status->canEditBy($current_user)){
            return  $this->unauthorized(); 
        }
        if($status->signupStopped()) $status->signup=0;
        else   $status->signup=1;
        
        if($status->classStopped()) $status->class=0;
        else   $status->class=1;
        
        return response()->json(['status' => $status ]);        
    }
    public function update(StatusRequest $request, $id)
    {
        $current_user=$this->currentUser();
        $status=Status::findOrFail($id);
        if($status->hasRemoved()) {
            abort(404);
        }
        if(!$status->canEditBy($current_user)){
            return  $this->unauthorized(); 
        }
        $updated_by=$current_user->id;
        $removed=false;
        $values=$request->getValues($updated_by,$removed);

        $signup=(int)$values['signup'];
        $class=(int)$values['class'];

        $status->update($values);

        return response()->json(['status' => $status ]);   
    }
    
   
   
}
