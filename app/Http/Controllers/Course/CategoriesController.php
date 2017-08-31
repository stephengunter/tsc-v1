<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

use App\Repositories\Categories;
use App\Http\Requests\Course\CategoryRequest;
use App\Support\Helper;
use App\Http\Middleware\CheckAdmin;

class CategoriesController extends BaseController
{
    protected $key='categories';
    public function __construct(Categories $categories,CheckAdmin $checkAdmin) 
    {
        // $exceptAdmin=['index','activeCategories'];
		$exceptAdmin=[];
        $allowVisitors=[];
        $this->setMiddleware( $exceptAdmin, $allowVisitors);
         
        $this->categories=$categories;
         
        $this->setCheckAdmin($checkAdmin);
		
	}

    public function index()
    {
        if(!request()->ajax()){
            $menus=$this->menus($this->key);            
            return view('categories.index')
                    ->with(['menus' => $menus]);
        } 

        $categories=$this->categories->getAll()
                                     ->orderBy('public','desc')
                                     ->orderBy('active','desc')
                                     ->orderBy('order','desc');
       
        return response()
            ->json([
                'model' => $categories->paginate(request()->per_page)
            ]);
       
    }
    public function create()
    {
        $category= $this->categories->initialize();
       
        return response()
            ->json([
                'category' => $category
            ]);
    }
    public function store(CategoryRequest $request)
    {
        $current_user=$this->checkAdmin->getAdmin();
        $updated_by=$current_user->id;
        $removed=false;

        $values=$request->getValues($updated_by,$removed);
        $category= $this->categories->store($values);

        return response()->json([
            'category' => $category
         ]);
      
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
    public function updateDisplayOrder(Request $request, $id)
    {
        $category=$this->categories->findOrFail($id);
        $current_user=$this->currentUser();
        if(!$category->canEditBy($current_user)){
            return  $this->unauthorized();         
        }

        $updated_by=$current_user->id;
        $up=$request['up'];
        
        $category=$this->categories->updateDisplayOrder($category ,$up, $updated_by);
        
        return response()
            ->json([
                'category' => $category
            ]);    

    }

    public function destroy($id)
    {
        $current_user=$this->currentUser();
        $category=$this->categories->findOrFail($id);
        if(!$category->canDeleteBy($current_user)){
            return  $this->unauthorized();   
        }
        $this->categories->delete($id,$current_user->id);

        return response()
            ->json([
                'deleted' => true
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
