<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Resume;

use App\Http\Requests\User\ResumeUserRequest; 

use App\Support\Helper;
use Carbon\Carbon;
use DB;



class ResumesController extends BaseController
{
  
    public function __construct()                     
    {
        $this->middleware('auth:api');

    }
    public function index()
    {
        $current_user=$this->currentUser();
        if($current_user->resume){
            return $this->edit($current_user->resume);           
        }else{
            return $this->create();
        }
      
    }
    private function create()
    {
        $current_user=$this->currentUser();
        $request = request();
        $typeId=(int)$request->type; 

        if($typeId){
            abort();
        }else{
            if($current_user->teacher && !$current_user->teacher->removed){
                return  response()->json(['msg' => '您的教師資料已存在' ]  ,  401);
            }

            $id=$current_user->id;
            $resume=Resume::find($id);
            if($resume) return $this->edit($id,$resume);

            $current_user->profile->photo=$current_user->profile->photo();
            $resume=Resume::initialize();
    
            return response()->json([
                'user' => $current_user,
                'resume' => $resume
            ]);
        }

    }
    private function edit(Resume $resume)
    {
        
        $current_user=$this->currentUser();

        if(!$resume->canEditBy($current_user)){
            return  $this->unauthorized();    
        }

        $current_user->profile->photo=$current_user->profile->photo();
        

        return response()->json([
                'user' => $current_user,
                'resume' => $resume
            ]);
        
    }
    public function show($id)
    {
             
        
    }
    public function store(ResumeUserRequest $request)
    {
        $current_user=$this->currentUser();
        $user=$current_user;
        $updated_by=$user->id;
        $removed=false;

        $resumeValues=$request->getResumeValues($updated_by,$removed);
        $userValues=$request->getUserValues($updated_by,$removed);
        $profileValues=$request->getProfileValues($updated_by,$removed);

        $user= DB::transaction(function() 
        use($user,$userValues,$profileValues,$resumeValues)
        {
            
            $user->update($userValues);
            $user->profile->update($profileValues);

            if($user->resume){
                $user->resume->update($resumeValues);
            }else{
                $resume=new Resume($resumeValues);  
                $user->resume()->save($resume);
       
            }
            return $user;
        });
        
        
        return response()->json($user); 
        
         
    }
    
    
}
