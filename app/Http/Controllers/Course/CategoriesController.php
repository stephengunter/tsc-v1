<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Category;
use App\Repositories\Categories;
use App\Http\Requests\Course\CategoryRequest;
use App\Support\Helper;

class CategoriesController extends BaseController
{
    protected $key='main_settings';
    public function __construct(Categories $categories) 
    {
        $this->categories=$categories;
		
	}

    public function index()
    {
        if(!request()->ajax()){
            $menus=$this->menus($this->key);            
            return view('categories.index')
                    ->with(['menus' => $menus]);
        } 

        $current_user=$this->currentUser();
        $canEdit=Category::canEdit($current_user);

        $categories=$this->categories->getAll()
                                     ->orderBy('public','desc')
                                     ->orderBy('active','desc')
                                     ->orderBy('order','desc')->get();
       

        return response()
            ->json([
                'categories' => $categories,
                'canEdit' => $canEdit
            ]);
       
    }
    public function create()
    {
        $current_user=$this->currentUser();
        if(!Category::canEdit($current_user)){
            return  $this->unauthorized(); 
        } 
        $category= $this->categories->initialize();
       
        return response()
            ->json([
                'category' => $category
            ]);
    }
    public function store(CategoryRequest $request)
    {
        $current_user=$this->currentUser();
        if(!Category::canEdit($current_user)){
            return  $this->unauthorized(); 
        }

       
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
        $current_user=$this->currentUser();
        $category=$this->categories->findOrFail($id);
       
        $category->canEdit=$category->canEditBy($current_user);
        $category->canDelete=$category->canDeleteBy($current_user);

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
    public function updateDisplayOrder(Request $form)
    {
        $current_user=$this->currentUser();

        $categories=$form['categories'];
        for($i = 0; $i < count($categories); ++$i) {
            $category=$categories[$i];

            $id=$category['id'];
            $order=$category['order'];
            $updated_by=$current_user->id;

            $this->categories->updateDisplayOrder($id,$order,$updated_by);
            
        }


        return response()->json(['success' => true]);

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
