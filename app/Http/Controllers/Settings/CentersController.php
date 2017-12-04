<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

use App\Repositories\Centers;
use App\Repositories\Users;
use App\Http\Requests\Settings\CenterRequest;

use App\Center;
use App\ContactInfo;
use App\Address;
use App\Support\Helper;

class CentersController extends BaseController
{
    protected $key='main_settings';
    public function __construct(Centers $centers, Users $users)
    {

        $this->centers=$centers;
        $this->users=$users;
       
	}

    public function index()
    { 
        
        if(!request()->ajax()){
            $areaOptions=$this->centers->getAllAreas();
            $menus=$this->menus($this->key);            
            return view('centers.index')
                    ->with(['menus' => $menus,
                            'areaOptions' => $areaOptions
                    ]);                
        }  

        $request=request();
        $oversea=(int)$request['oversea'];
        $area_id=(int)$request['area'];


       
        
        $centers=$this->centers->index($oversea, $area_id)->get();
        
      
        foreach ($centers as $center) {
            $center->populateViewData();

            $center->error='';
            
        }
        
        return response()
            ->json([
                'centers' => $centers
            ]);
       
    }
    public function create()
    {
        if(!request()->ajax()){
           
            $menus=$this->menus($this->key);            
            return view('centers.create')
                    ->with(['menus' => $menus]);                  
        }  

        $center= Center::initialize();
        $contact_info= ContactInfo::initialize();
        return response()
            ->json([
                'center' => $center,
                'contact_info' => $contact_info
            ]);
    }
    public function store(CenterRequest $request)
    {
        $current_user=$this->currentUser();
        $updated_by=$current_user->id;
        $removed=false;

        $centerValues=$request->getCenterValues($updated_by,$removed);
        $contactInfoValues=$request->getContactInfoValues($updated_by);

        $center=null;
        if($request->isOversea())
        {
            $tel=$contactInfoValues['tel'];
            $fax=$contactInfoValues['fax'];
            $street=$contactInfoValues['addressText'];
            $center= $this->centers->createOverseaCenter($centerValues,$tel,$fax,$street,$updated_by);
        }else{
            $contactInfo=ContactInfo::create($contactInfoValues);
            
            $centerValues['contact_info']=$contactInfo->id;
            $center=$this->centers->store($centerValues);
        }
        
        
        

        return response()->json($center);
      
    }
    public function show($id)
    {
        
        if(!request()->ajax()){
            $menus=$this->menus($this->key);            
            return view('centers.details')
                    ->with(['menus' => $menus , 'id' => $id ]); 
                  
        }  

        $center=$this->centers->findOrFail($id);        
        $current_user=$this->currentUser();

        $area=$this->centers->findArea($center->area_id);
        if($area)  $center->areaName=$area['text'];

        

        $center->canEdit=$center->canEditBy($current_user);
        $center->canDelete=$center->canDeleteBy($current_user);
        
        return response()->json(['center' => $center ]);  
       
    }
    public function edit($id)
    {
        $center=$this->centers->findOrFail($id);    
        $current_user=$this->currentUser();
        if(!$center->canEditBy($current_user)){
            return  $this->unauthorized();       
        }

        $areas=$this->centers->getAllAreas();

        return response()->json([
                                    'center' => $center ,
                                    'areas' => $areas
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
        $values= $request->getCenterValues($updated_by,$removed);

         

        $center->update($values);

        return response()->json($center);
    }
    public function updateDisplayOrder(Request $form)
    {
       
        $current_user=$this->currentUser();
        
        $centers=$form['centers'];
        for($i = 0; $i < count($centers); ++$i) {
            $center=$centers[$i];

            $id=$center['id'];
            $order=$center['order'];
            $updated_by=$current_user->id;

            $this->centers->updateDisplayOrder($id,$order,$updated_by);
            
        }


        return response()->json(['success' => true]);

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

    // public function adminCenterOptions()
    // {
    //     $current_user=$this->checkAdmin->getAdmin();
    //     $validCenters=$current_user->admin->validCenters();
    //     $options=$this->centers->optionsConverting($validCenters);
    //           return response()
    //         ->json([
    //             'options' => $options
    //         ]);
    // }

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
