<?php

namespace App\Repositories;

use App\Center;
use App\ContactInfo;
use Excel;
use DB;

class Centers 
{
    
    public function getAll()
    {
         return Center::where('removed',false);
    }
    public function findOrFail($id)
    {
        $center = Center::findOrFail($id);
        return $center;
       
    }
    public function getById($id){
        return Center::find($id);
    }
    
    public function store($values)
    {
        $center=Center::create($values);
        return  $center;
      
    }

    
   
    public function updateDisplayOrder($id,$order,$updated_by)
    {
            $center=$this->findOrFail($id);          
           
            $center->display_order= (int)$order;           
            $center->updated_by= $updated_by;

            if($order>=0){
                $center->active=true;
            }else{
                $center->active=false;
            }
            
            
            $center->save();
           
            return $center;

    }
    
   

    public function options($empty_item=false)
    {
        $centerList=$this->getAll()->get();
        return $this->optionsConverting($centerList,$empty_item);
       
    }

    public function optionsConverting($centerList,$empty_item=false)
    {
        $centerOptions=[];

        if($empty_item){
            $item=[ 'text' => '全部開課中心' , 
                    'value' => 0 , 
                  ];
            array_push($centerOptions,  $item);
        }

        foreach($centerList as $center)
        {
            $item=[ 'text' => $center->name , 
                     'value' => $center->id , 
                 ];
            array_push($centerOptions,  $item);
        }

        return $centerOptions;
    }
    public function delete($id,$admin_id)
    {
        $center = Center::findOrFail($id);

         $values=[
            'active' =>0,
            'removed' => 1,
            'updated_by' => $admin_id
         ];
        
         $center->update($values);
        
    }
    public function activeCenters()
    {
       return  $this->getAll()->where('active',true)
                ->orderBy('display_order','desc');
        
    }

    public function importCenters($file,$current_user)
    {

        Excel::load($file, function($reader) use ($current_user){
            $centerList=$reader->get()->toArray()[0];
            for($i = 1; $i < count($centerList); ++$i) {
                $row=$centerList[$i];
                
                $name=trim($row['name']);
                if(!$name){
                   continue;
                }
 
                $exist_center=$this->getAll()->where('name',$name)->first(); 

                $code=trim($row['code']);    
                            
                $order=(int)trim($row['order']);
                $active=true;
                if($order>=0){
                    $active=true;
                }else{
                    $active=false;
                }

                $zipcode=trim($row['zipcode']);
                $street=trim($row['street']);
                $tel=trim($row['tel']);
                $fax=trim($row['fax']);
               

                $updated_by=$current_user->id;

                $values=[
                    'name' => $name,
                    'code' => $code,
                    'display_order' => $order,
                    'active' => $active,
                    'updated_by' => $updated_by
                ];

                if($exist_center){
                    $exist_center->updateContactInfo($tel ,$fax, $zipcode, $street,$updated_by);                    
                    $exist_center->update($values);
                }else{
                    $contactInfo=ContactInfo::createByAddress($tel,$fax,$zipcode, $street, $updated_by);
                    if($contactInfo) $values['contact_info'] = $contactInfo->id;
                    $center=Center::create($values);
                
                }
            }  //end for  
 
           
        });
    }
  
   
   
    
}