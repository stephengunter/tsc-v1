<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Repositories\Categories;
use App\Http\Requests\CategoryRequest;
use App\Support\Helper;
use App\Http\Middleware\CheckAdmin;

class CategoriesController extends Controller
{
    public function __construct(Categories $categories,CheckAdmin $checkAdmin) 
    {
         $exceptAdmin=['index','activeCategories'];
		 $this->middleware('admin', ['except' => $exceptAdmin ]);
         $this->checkAdmin=$checkAdmin;
         

		 $this->categories=$categories;
	}

    public function index()
    {
        $categories=$this->categories->getAll()->orderBy('public','desc')
                                     ->orderBy('active','desc')->orderBy('order','desc');
       
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
         return response()
                ->json([
                    'category' => $category
                ]);        
    }
    public function update(CategoryRequest $request, $id)
    {
        $current_user=$this->checkAdmin->getAdmin();
        $updated_by=$current_user->id;
        $removed=false;
        $values=$request->getValues($updated_by,$removed);
        $category= $this->categories->update($values,$id);

         return response()
                ->json([
                    'category' => $category
                ]);   
    }
    public function updateDisplayOrder(Request $request, $id)
    {
        $current_user=$this->checkAdmin->getAdmin();
        $updated_by=$current_user->id;
        $up=$request['up'];
        
        $category=$this->categories->updateDisplayOrder($up, $id,$updated_by);
        
        return response()
            ->json([
                'category' => $category
            ]);    

    }

    public function destroy($id)
    {
        $current_user=$this->checkAdmin->getAdmin();
        $category=$this->categories->findOrFail($id);
        if(!$category->canDelete()){
            return   response()->json(['msg' => '權限不足' ]  ,  401);    
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
