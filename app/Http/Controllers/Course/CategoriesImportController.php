<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

use App\Category;

use App\Repositories\Categories;

use App\Support\Helper;

use Illuminate\Support\Facades\Input;

class CategoriesImportController extends BaseController
{
    protected $key='main_settings';
    public function __construct(Categories $categories) 
    {
        $this->categories=$categories;
		
	}
    

    public function index()
    {
        if(request()->ajax()) abort(404);
        $current_user=$this->currentUser();
        if(!Category::canImport($current_user)){
            return $this->unauthorized();
        }
       
        $menus=$this->menus($this->key);            
        return view('categories.import')->with([ 'menus' => $menus ]);        
       
    
    }

    public function store(Request $form)
    {
        
        $current_user=$this->currentUser();
        if(!Category::canImport($current_user)){
            return $this->unauthorized();
        }
        

        if(!$form->hasFile('categories_file')){
            return   response()
                        ->json(['categories_file' => ['無法取得上傳檔案'] 
                            ]  ,  422);      
        }

        $file=Input::file('categories_file');   

        $err_msg=$this->categories->importCategories($file,$current_user);
        
         if($err_msg) {
              return response()->json(['error' => $err_msg,'code' => 422 ], 422);
          
         }

        return response()->json(['success' => true]);

       
    }
    

    



    
   
}
