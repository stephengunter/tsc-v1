<?php

namespace App\Repositories;

use App\Center;

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
   
     public function updateDisplayOrder($up, $id)
    {
            $center=$this->findOrFail($id);          
            $num= rand(1, 10);
            if($up){
                $center->display_order += $num;
            }else{
                $center->display_order -= $num;
            }
            
            $center->save();
           
            return $center;

    }
    
   

    public function options()
    {
        $centerList=$this->getAll()->get();
        return $this->optionsConverting($centerList);
       
    }

    public function optionsConverting($centerList)
    {
        $centerOptions=[];
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
  
   
   
    
}