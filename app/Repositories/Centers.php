<?php

namespace App\Repositories;

use App\Center;
use App\ContactInfo;
use App\Address;
use Excel;
use DB;

use App\Support\Helper;

class Centers 
{
    
    public function getAll()
    {
         return Center::where('removed',false);
    }
    public function hasCoursesCenters()
    {
         return $this->getAll()->has('courses');
    }
    public function findOrFail($id)
    {
        $center = Center::findOrFail($id);
        return $center;
       
    }
    public function getById($id){
        return Center::find($id);
    }
    public function getByCode($code)
    {
        $code=strtoupper($code);
        return $this->getAll()->where('code',$code)->first();
    }

    public function index(bool $oversea)
    {
        return $this->getAll()->where('oversea',$oversea)
                              ->orderBy('display_order','desc');
        
    }
    
    public function store($centerValues)
    {
        $center=new Center ($centerValues);
        if($center->display_order>=0){
            $center->active=true;
        }else{
            $center->active=false;
        }

        
        $center->save();
        
        return  $center;
      
    }

    public function createOverseaCenter(array $centerValues,$tel,$fax,$street,$updated_by)
    {
        $addressId=0;
        if($street){
            $address=Address::create([
                'streetAddress' => $street,
                'updated_by' => $updated_by
            ]); 
            $addressId=$address->id;
        }
        $contactInfo=ContactInfo::create([
            'tel' => $tel,
            'fax' => $fax,
            'contactAddress' => $addressId,
            'updated_by' => $updated_by
        ]);

        $centerValues['contact_info']=$contactInfo->id;
        $centerValues['oversea']=true;

        return $this->store($centerValues);

        
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
        $isOversea=false;
        $centerList=$this->index($isOversea)->get();
        return $this->optionsConverting($centerList,$empty_item);
       
    }

    public function optionsConverting($centers,$empty_item=false)
    {
        return Center::optionsConverting($centers,$empty_item);
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
        $err_msg='';

        $excel=Excel::load($file, function($reader) {             
            $reader->limitColumns(16);
            $reader->limitRows(100);
        })->get();

        $centerList=$excel->toArray()[0];
        for($i = 1; $i < count($centerList); ++$i) {
            $row=$centerList[$i];
            
            $name=trim($row['name']);
            if(!$name){
               continue;
            }

            $code=trim($row['code']);  
            if(!$code){
                $err_msg .= '必須填寫中心代碼';
                continue;
            }
            
            $exist_center=$this->getByCode($code);

            $order=(int)trim($row['order']);
            $active=true;
            if($order>=0){
                $active=true;
            }else{
                $active=false;
            }

            $course_tel=trim($row['course_tel']);
            
            $zipcode=trim($row['zipcode']);
            $street=trim($row['street']);
            $tel=trim($row['tel']);
            $fax=trim($row['fax']);


            
            $oversea=(int)trim($row['oversea']) > 0 ? true:false;

            $updated_by=$current_user->id;
            
            $values=[
                'name' => $name,
                'code' => $code,
                'oversea' => $oversea,
                'course_tel' => $course_tel,
                'display_order' => $order,
                'active' => $active,
                'updated_by' => $updated_by
            ];


            if($oversea){
                if($exist_center){
                    $exist_center->updateContactInfo($tel ,$fax, $zipcode, $street,$updated_by);                    
                    $exist_center->update($values);
                }else{
                    $center= $this->createOverseaCenter($values,$tel,$fax,$street,$updated_by);
                }
                
            }else{
                if($exist_center){
                    $exist_center->updateContactInfo($tel ,$fax, $zipcode, $street,$updated_by);                    
                    $exist_center->update($values);
                }else{
                    
                    $contactInfo=ContactInfo::createByAddress($zipcode, $street, $updated_by,$tel,$fax);
                    if($contactInfo) $values['contact_info'] = $contactInfo->id;
                   
                    $center=$this->store($values);
                
                }
            }

        }  //end for  

        return $err_msg;
    }
  
   
   
    
}