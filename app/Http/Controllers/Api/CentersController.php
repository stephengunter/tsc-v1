<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;

use App\Repositories\Centers;
use App\Repositories\Users;
use App\Http\Requests\Settings\CenterRequest;

use App\Center;

use App\Http\Middleware\CheckAdmin;
use App\Support\Helper;

use DB;

class CentersController extends BaseController
{
    public function __construct(Centers $centers, Users $users)
    {
        $this->centers=$centers;
        $this->users=$users;

        
	}

    public function index()
    {
        
        $centers=$this->centers->activeCenters()->get();
      
        foreach ($centers as $center) {
            $center->contactInfo= $center->contactInfo();
            $center->photo= $center->photo();
            if($center->contactInfo)
            {
                $center->contactInfo->addressA=$center->contactInfo->addressA();
            }
        }
        
        return response()->json(['centers' => $centers ]);
       
    }
   
    public function show($id)
    {
        
        if(!request()->ajax()){
            return view('centers.details')->with([ 'id' => $id ]);          
        }  

        $center=$this->centers->findOrFail($id);        
        $current_user=$this->currentUser();

        $center->canEdit=$center->canEditBy($current_user);
        $center->canDelete=$center->canDeleteBy($current_user);
        
         return response()
                ->json([
                    'center' => $center
                ]);
       
    }
    public function edit($id)
    {
        $center=$this->centers->findOrFail($id);    
        $current_user=$this->currentUser();
        if(!$center->canEditBy($current_user)){
            return  $this->unauthorized();       
        }

         return response()
                ->json([
                    'center' => $center
                ]);        
    }
    public function update(CenterRequest $request, $id)
    {
         $center=$this->centers->findOrFail($id);    
         $current_user=$this->currentUser();
         if(!$center->canEditBy($current_user)){
             return  $this->unauthorized();      
         }
         $updated_by=$current_user->id;
         $removed=false;
         $values= $request->getValues($updated_by,$removed);
         $center->update($values);

          return response()->json($center);
    }
    public function updateDisplayOrder(Request $request, $id)
    {
            $up=$request['up'];
            $center=$this->centers->updateDisplayOrder($up, $id);

            return response()->json($center);

    }
    public function updatePhoto(Request $request, $id)
    {
        $center=$this->centers->findOrFail($id);
        $current_user=$this->currentUser();
        if(!$center->canEditBy($current_user)){
            return  $this->unauthorized();  
        }

        
        $center->photo_id=$request['photo_id'];
        $center->updated_by=$current_user->id;
        $center->save();
           
        return response()->json(['saved' => true ]);            

    }
    public function updateContactInfo(Request $request, $id)
    {
        $center=$this->centers->findOrFail($id);
        $current_user=$this->currentUser();
        if(!$center->canEditBy($current_user)){
             return  $this->unauthorized();  
        }

        $contact_info=$request['contact_info'];
        $values=Helper::setUpdatedBy(['contact_info'=>$contact_info],$current_user->id);
        
        $center->update($values);
           
            return response()
                    ->json([
                        'saved' => true
                    ]);   
          

    }

    public function destroy($id)
    {
        $center=$this->centers->findOrFail($id);
        $current_user=$this->currentUser();
        if(!$center->canDeleteBy($current_user)){
             return  $this->unauthorized();  
        }

        $this->centers->delete($id ,$current_user->id);

        return response() ->json(['deleted' => true ]);
         
    }
   
    

    public function options()
    {
        // $current_admin=$this->currentAdmin();
        // if($current_admin){
        //     $centers=$current_admin->validCenters();
        //     $options=$this->centers->optionsConverting($centers);
        //     return response()->json([ 'options' => $options]);
        // }
        $options=$this->centers->options();
            return response()
            ->json([
                'options' => $options
            ]);
    }

    public function adminCenterOptions()
    {
        $current_user=$this->checkAdmin->getAdmin();
        $validCenters=$current_user->admin->validCenters();
        $options=$this->centers->optionsConverting($validCenters);
              return response()
            ->json([
                'options' => $options
            ]);
    }

    public function activeCenters()
    {
         $centers=$this->centers->activeCenters()->get();
       
        foreach ($centers as $item) {
             $item->contactInfo= $item->contactInfo();
             $item->photo= $item->photo();
             if($item->contactInfo)
             {
                 $item->contactInfo->addressA=$item->contactInfo->addressA();
             }
            
        }
        
        return response()
            ->json([
                'centers' => $centers
            ]);
    }

    
}
