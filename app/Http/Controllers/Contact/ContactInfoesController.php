<?php

namespace App\Http\Controllers\Contact;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

use App\ContactInfo;
use App\User;
use App\Http\Middleware\CheckAdmin;
use App\Support\Helper;

use App\Events\ContactInfoCreated;

class ContactInfoesController extends BaseController
{
    
    public function show($id)
    {   
        
        $contactInfo=ContactInfo::findOrFail($id);
        
        $current_user=$this->currentUser();
        if(!$contactInfo->canViewBy($current_user)){
              return $this->unauthorized();;
        }

        
        $contactInfo->addressA=$contactInfo->addressA();
        $contactInfo->addressB=$contactInfo->addressB();
       
        $canEdit=$contactInfo->canEditBy($current_user);
        $contactInfo->canEdit=$canEdit;
        $contactInfo->canDelete=$canEdit;

         return response()
                ->json([
                    'contactInfo' => $contactInfo
                ]);
    }
     
     public function create()
     {
        $contactInfo=ContactInfo::initialize();
          return response()->json([
                'contactInfo' => $contactInfo
            ]);  
           
     }

    public function edit($id)
    {
        $contactInfo=ContactInfo::findOrFail($id);

        $current_user=request()->user();
        if(!$contactInfo->canEditBy($current_user)){
              return $this->unauthorized();
        }
      
         return response()->json([
                'contactInfo' => $contactInfo
            ]);     
    }

   

    public function store(Request $request)
    {
        $current_user=request()->user();
        $values=Helper::setUpdatedBy($request['contactInfo'],$current_user->id);
        
        $contactInfo=ContactInfo::create($values);

        $user_id=0;        
        if(array_key_exists ( 'user_id' ,$values)){
            $user_id=(int)$values['user_id'];
        }
      
        $center_id=0;        
        if(array_key_exists ( 'center_id' ,$values)){
            $center_id=(int)$values['center_id'];
        }

        event(new ContactInfoCreated($contactInfo,$current_user,$user_id,$center_id));  

        return response()->json($contactInfo); 
    }

    public function update(Request $request, $id)
    {
         $contactInfo=ContactInfo::findOrFail($id);

         $current_user=request()->user();
         if(!$contactInfo->canEditBy($current_user)){
              return  $this->unauthorized();
         }
         
         $values=Helper::setUpdatedBy($request['contactInfo'],$current_user->id);
         $contactInfo->update($values);

         return response()->json($contactInfo); 
    }

    public function destroy($id)
    {
         $contactInfo=ContactInfo::findOrFail($id);

         $current_user=request()->user();
         if(!$contactInfo->canEditBy($current_user)){
              return  $this->unauthorized();
         }
         $user=$contactInfo->getUser();
         if($user){
            $values=Helper::setUpdatedBy(['contact_info'=> '' ],$current_user->id);
            $user->update($values);
         
         }
        
         $center=$contactInfo->getCenter();
         if($center){
            $values=Helper::setUpdatedBy(['contact_info'=> ''],$current_user->id);
            $center->update($values);
         }

         $contactInfo->delete();

         return response()
            ->json([
                'deleted' => true
            ]);
    }
    
    
}
