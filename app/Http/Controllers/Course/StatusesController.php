<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

use App\Status;
use App\Course;

use App\Support\Helper;
use App\Http\Middleware\CheckAdmin;

class StatusesController extends BaseController
{
   
    public function __construct(CheckAdmin $checkAdmin) 
    {
       
		$exceptAdmin=[];
        $allowVisitors=[];
        $this->setMiddleware( $exceptAdmin, $allowVisitors);
         
        $this->setCheckAdmin($checkAdmin);
		
	}
    
    public function show($id)
    {
        $category=$this->categories->findOrFail($id);
        $category->canEdit=true;
        $category->canDelete=$category->canDelete();

         return response()
                ->json([
                    'category' => $category
                ]);
       
    }
    public function edit($id)
    {
        $category=$this->categories->findOrFail($id);    
        $current_user=$this->currentUser();
        if(!$category->canEditBy($current_user)){
            return  $this->unauthorized(); 
        }
        return response()
                ->json([
                    'category' => $category
                ]);        
    }
    public function update(CategoryRequest $request, $id)
    {
        $category=$this->categories->findOrFail($id); 
        $current_user=$this->currentUser();
        if(!$category->canEditBy($current_user)){
            return  $this->unauthorized();         
        }
        $updated_by=$current_user->id;
        $removed=false;
        $values=$request->getValues($updated_by,$removed);

        $category->update($values);

         return response()
                ->json([
                    'category' => $category
                ]);   
    }
    
   
   
}
