<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;

use App\Repositories\Categories;
use App\Http\Requests\Course\CategoryRequest;
use App\Support\Helper;

class CategoriesController extends BaseController
{
   
    public function __construct(Categories $categories) 
    {
       
        $this->categories=$categories;
		
	}

    public function index()
    {
        $categories=$this->categories->activeCategories()
                                     ->orderBy('public','desc')
                                     ->orderBy('order','desc')
                                     ->get();

        return response()->json(['categories' => $categories]); 
       
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

    public function options()
    {
         $request = request();
         $public=(int)$request->public;

         $options=$this->categories->options($public);

          return response()
            ->json([
                'options' => $options
            ]);
    }
    public function allOptions()
    {
         $all=$this->categories->getAll()->get();
         $options=$this->categories->optionsConverting($all);

          return response()
            ->json([
                'options' => $options
            ]);
    }

    public function activeCategories()
    {
        $categories=$this->categories->getAll()->orderBy('public','desc')
                                     ->orderBy('active','desc')
                                     ->orderBy('order','desc')->get();

          return response()
            ->json([
                'categories' => $categories
            ]);                     
    }
   
}
