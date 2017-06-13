<?php

namespace App\Repositories;

use App\Category;

class Categories 
{
    public function initialize()
    {
        return [
			 'name' => '',
			 'parent' => 0,
		     'order' =>  0,
             'icon'=>'',
             'public' => 0,
			 'active' => false,
        ];
    }
    public function getAll()
    {
         return Category::where('removed',false);
    }
    
    public function findOrFail($id)
    {
        $category = Category::findOrFail($id);
        return $category;       
    }
    public function store($values)
    {
        $category=Category::create($values);
        return  $category;
      
    }
    public function update($values, $id)
    {
         $category=Category::findOrFail($id);     

         $category->update($values);

           return $category;
    }
    public function options($public)
    {
        $options=[];
        $categoryList=[];
        if($public){
            $categoryList=$this->publicCategories();
        }else{
            $categoryList=$this->privateCategories();
        }
        $categoryList=$categoryList->where('active',true)->get();
       
          return $this->optionsConverting($categoryList);
    }
    public function optionsConverting($categoryList)
    {
        $options=[];
        foreach($categoryList as $category)
        {
            $item=[ 'text' => $category->name , 
                     'value' => $category->id , 
                 ];
            array_push($options,  $item);
        }
          return $options;
    }

    public function publicCategories()
    {
       return $this->getAll()->where('public',true)->orderBy('order','desc');
    }

    public function privateCategories()
    {
       return $this->getAll()->where('public',false)->orderBy('order','desc');
    }
    public function updateDisplayOrder($up, $id,$updated_by)
    {
            $category=Category::findOrFail($id); 
          
            $num= rand(1, 10);
            if($up){
                $category->order += $num;
            }else{
                $category->order -= $num;
            }

            $category->updated_by=$updated_by;
            
            $category->save();
           
            return $category;

    }

    

    public function delete($id,$admin_id)
    {
         $category = Category::findOrFail($id);

         $values=[
            'active' =>0,
            'removed' => 1,
            'updated_by' => $admin_id
         ];
        
         $category->update($values);
        
    }
     public function activeCategories()
     {
        
     }
   
   
    
}