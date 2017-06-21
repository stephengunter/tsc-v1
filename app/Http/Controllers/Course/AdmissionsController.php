<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

use App\Http\Requests\Course\AdmissionRequest;

use App\Admission;
use App\Course;

use App\Support\Helper;
use App\Http\Middleware\CheckAdmin;

class AdmissionsController extends BaseController
{
    protected $key='admissions';
    public function __construct(CheckAdmin $checkAdmin) 
    {
       
		$exceptAdmin=[];
        $allowVisitors=[];
        $this->setMiddleware( $exceptAdmin, $allowVisitors);
         
        $this->setCheckAdmin($checkAdmin);
		
	}

    public function index()
    {
        $menus=$this->menus($this->key);            
            return view('admissions.index')
                    ->with(['menus' => $menus]); 
    }
    
    public function show($id)
    {
        $current_user=$this->currentUser();
        $admission=Admission::findOrFail($id);
        if($admission->hasRemoved()) {
            abort(404);
        }
        $admission->canEdit=$admission->canEditBy($current_user);
        $admission->canDelete=$admission->canDeleteBy($current_user);

        return response()->json(['admission' => $admission ]);   
       
    }
    public function edit($id)
    {
        $current_user=$this->currentUser();
        $admission=Admission::findOrFail($id);
        if($admission->hasRemoved()) {
            abort(404);
        }
        if(!$admission->canEditBy($current_user)){
            return  $this->unauthorized(); 
        }
        if($admission->signupStopped()) $admission->signup=0;
        else   $admission->signup=1;
        
        if($admission->classStopped()) $admission->class=0;
        else   $admission->class=1;
        
        return response()->json(['admission' => $admission ]);        
    }
    public function update(AdmissionRequest $request, $id)
    {
        $current_user=$this->currentUser();
        $admission=Admission::findOrFail($id);
        if($admission->hasRemoved()) {
            abort(404);
        }
        if(!$admission->canEditBy($current_user)){
            return  $this->unauthorized(); 
        }

        $updated_by=$current_user->id;
        $values=$request->getValues();

        $admission->ps=$values['ps'];
        $admission->updated_by=$updated_by;

        $signup=(int)$values['signup'];
        $class=(int)$values['class'];

        if($class==0){
            $admission->class=0;
            $admission->signup=0;           
        }else if($signup==0){
            $admission->signup=0;
        } 

        $admission->updateAdmission();

        return response()->json(['admission' => $admission ]);   
    }
    
   
   
}
