<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Http\Requests\Settings\TitleRequest;

use App\Title;

use App\Http\Middleware\CheckAdmin;
use App\Support\Helper;

class TitlesController extends BaseController
{
    protected $key='users';
    public function __construct(CheckAdmin $checkAdmin) {
      
		$exceptAdmin=[];
        $allowVisitors=[];
        $this->setMiddleware( $exceptAdmin, $allowVisitors);


        $this->setCheckAdmin($checkAdmin);
	}
   
    public function index()
    {
        if(!request()->ajax()){
            $menus=$this->menus($this->key);            
            return view('settings.title')
                    ->with(['menus' => $menus]);
        }  
        

        $titleList=Title::all();
        if(count($titleList)){
            $current_user=$this->currentUser();
            foreach ($titleList as $title) {
                $title->canDelete = $title->canDeleteBy($current_user);
                $title->canEdit = $title->canEditBy($current_user);
            }
        }
       
        return response()
            ->json([
                'titleList' => $titleList
            ]);
    }
    public function create()
    {
        $title=Title::initialize();
        
        return response()->json([ 'title' => $title ]);
    }
    public function store(TitleRequest $request)
    {
        $values=$request['title'];
       
        $title=Title::create($values);
        return response()->json($title);
    }
    public function edit($id)
    {
        $current_user=$this->currentUser();
        $title=Title::findOrFail($id);
        if(!$title->canEditBy($current_user)){
            return  $this->unauthorized();    
        }
      
        return response()->json([
            'title' => $title
        ]);
        
    }
    public function update(TitleRequest $request, $id)
    {
        $title=Title::findOrFail($id); 
        $current_user=$this->currentUser();
        if(!$title->canEditBy($current_user)){
            return  $this->unauthorized();    
        }
        $values=$request['title'];
        $title->update($values);

        return response()->json($title);
    }

    public function destroy($id)
    {
        $title=Title::findOrFail($id); 
        $current_user=$this->currentUser();
        if(!$title->canDeleteBy($current_user)){
            return  $this->unauthorized();    
        }

     
        $title->delete();

        return response()
            ->json([
                'deleted' => true
            ]);
    }
    
}
