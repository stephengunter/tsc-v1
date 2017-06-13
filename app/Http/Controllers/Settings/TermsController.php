<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\BaseController;

use Illuminate\Http\Request;
use App\Http\Requests\Settings\TermRequest;

use App\Term;

use App\Repositories\Terms;
use App\Http\Middleware\CheckAdmin;
use App\Support\Helper;

class TermsController extends BaseController
{
   
    public function __construct(Terms $terms, CheckAdmin $checkAdmin)
    {
	    $exceptAdmin=[];
        $allowVisitors=[];
		$this->setMiddleware( $exceptAdmin, $allowVisitors);


		$this->terms=$terms;
        
        $this->setCheckAdmin($checkAdmin);
	}

    public function index()
    {
        
        if(!request()->ajax()){
              
            return view('settings.term');
                    
        }
        
        $termList=$this->terms->getAll()
                              ->orderBy('active','desc')->orderBy('number','desc')
                               ->get();

        if(count($termList)){
            $current_user=$this->currentUser();
            foreach ($termList as $term) {
                $term->canDelete = $term->canDeleteBy($current_user);
                $term->canEdit = $term->canEditBy($current_user);
            }
        }
        
        
        return response() ->json(['termList' => $termList ]);          
    }

    public function create()
    {
        $term=Term::initialize();
        
        return response()->json([ 'term' => $term ]);
    }
   
    public function store(TermRequest $request)
    {
        $current_user=$this->currentUser();
        $updated_by=$current_user->id;
        $removed=false;
        $values=$request->getValues($updated_by,$removed);

        $number=$values['number'];
        $id=0;
        $valid=$this->terms->checkNumber($number,$id);       
        if(!$valid){
            return response()->json([
                              'term.number' => ['順序與現有資料重複'] 
                             ]  ,  422);
        }

        $term= $this->terms->store($values);
        return response() ->json($term);
    }
    public function edit($id)
    {
        $current_user=$this->currentUser();
        $term=$this->terms->findOrFail($id);
        if(!$term->canEditBy($current_user)){
            return  $this->unauthorized();    
        }
      
        return response()->json([
            'term' => $term
        ]);
        
    }
    public function update(TermRequest $request, $id)
    {
        $current_user=$this->currentUser();
        $term=$this->terms->findOrFail($id);
        if(!$term->canEditBy($current_user)){
            return  $this->unauthorized();    
        }

        $updated_by=$current_user->id;
        $removed=false;
        $values=$request->getValues($updated_by,$removed);

        $number=$values['number'];
        $id=$values['id'];
        $valid=$this->terms->checkNumber($number,$id);
       
        if(!$valid){
            return response()->json([
                              'term.number' => ['順序與現有資料重複'] 
                             ]  ,  422);
        }

        $term= $this->terms->update($values,$id);

        return response() ->json($term);
           
    }

    public function destroy($id)
    {
       $term=$this->terms->findOrFail($id); 
       $current_user=$this->currentUser();
       if(!$term->canDeleteBy($current_user)){
           return  $this->unauthorized();      
       }
       
       $this->terms->delete($id, $current_user->id);

        return response()
            ->json([
                'deleted' => true
            ]);
    }

    public function options()
    {
        $options=$this->terms->options();
         return response()
            ->json([
                'options' => $options
            ]);
    }



   



}
