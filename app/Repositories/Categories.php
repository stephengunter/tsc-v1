<?php

namespace App\Repositories;

use App\Category;

use Excel;

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

    public function getByCode($code)
    {
        $code=strtoupper($code);
        return $this->getAll()->where('code',$code)->first();
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

    public function topCategories()
    {
       return $this->getAll()->where('public',true);
    }
    public function normalCategories()
    {
       return $this->getAll()->where('public',false);
    }

    public function publicCategories()
    {
       return $this->getAll()->where('public',true)->orderBy('order','desc');
    }

    public function privateCategories()
    {
       return $this->getAll()->where('public',false)->orderBy('order','desc');
    }
    public function updateDisplayOrder($id,$order,$updated_by)
    {
        $category = Category::findOrFail($id);        

        $category->order= (int)$order;           
        $category->updated_by= $updated_by;

        if($order>=0){
            $category->active=true;
        }else{
            $category->active=false;
        }
        
        
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
        return $this->getAll()->where('active',true);
    }
    public function findByName($name)
    {
        return $this->activeCategories()->where('name',$name)->first();
    }
    public function findByCode($code)
    {
        return $this->activeCategories()->where('code',$code)->first();
    }
    public function groupCategory()
    {
        return Category::where('code','group')->first();
    }
   
    public function importCategories($file,$current_user)
    {
        $err_msg='';

        $excel=Excel::load($file, function($reader) {             
            $reader->limitColumns(16);
            $reader->limitRows(100);
        })->get();

        $categoryList=$excel->toArray()[0];
        for($i = 1; $i < count($categoryList); ++$i) {
            $row=$categoryList[$i];
            
            $name=trim($row['name']);
            if(!$name){
               continue;
            }

            $exist_category=null;

            $code=trim($row['code']);
            $top=(int)trim($row['top']);  
            $public=false;
            if($top>0){
                $public=true;
                $exist_category=$this->topCategories()
                ->where('name',$name)->first();
            }else{
                $public=false;
                if(!$code){
                    $err_msg .= '必須填寫分類代碼';
                    continue;
                }
                $exist_category=$this->normalCategories()
                              ->where('code',$code)->first();
            }
             
                        
            $order=(int)trim($row['order']);
            $active=true;
            if($order>=0){
                $active=true;
            }else{
                $active=false;
            }
           
            $icon=trim($row['icon']);
            $updated_by=$current_user->id;

            $values=[
                'name' => $name,
                'code' => $code,
                'order' => $order,
                'icon' => $icon,
                'public' => $public,
                'active' => $active,
                'updated_by' => $updated_by
            ];

            if($exist_category){                                    
               
                $exist_category->update($values);
            
            }else{                
                $category=Category::create($values);
            
            }
        }  //end for  

        return $err_msg;
    }
    
}