<?php

namespace App\Repositories;

use App\Discount;

class Discounts 
{
    public function getAll()
    {
       return Discount::where('removed',false);
         
    }
    public function activeDiscounts()
    {
       return $this->getAll()->where('active',true);
         
    }


    public function findOrFail($id)
    {
       return Discount::findOrFail($id); 
         
    }

    public function getByKey($key)
    {
        $this->getAll()->where('key',$key)->first();
    }
    
    public function store($values)
    {
          $discount=Discount::create($values);

          return $discount;
    }
    public function update($values, $id)
    {
         $discount=Discount::findOrFail($id); 
         $discount->update($values);

          return $discount;
    }
    
    
    public function delete($id,$admin_id)
    {
        $discount=Discount::findOrFail($id); 

         $values=[
            'active' =>0,
            'removed' => 1,
            'updated_by' => $admin_id
         ];
        
         $discount->update($values);
        
    }

    
    public function optionsConverting($discounts)
    {
        $options=[];
        foreach($discounts as $discount)
        {
             $item=[ 'text' => $discount->name , 
                     'value' => $discount->id ,
                     'selected' => false 
                 ];
            array_push($options,  $item);
        }
          return $options;
    }
   
    
}