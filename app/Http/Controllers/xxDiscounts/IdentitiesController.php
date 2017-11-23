<?php

namespace App\Http\Controllers\Discounts;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Http\Requests\Discounts\IdentityRequest;

use App\Identity;

use App\Repositories\Identities;

use App\Http\Middleware\CheckAdmin;
use App\Support\Helper;

class IdentitiesController extends BaseController
{
    protected $key='identities';
    public function __construct(Identities $identities,CheckAdmin $checkAdmin) {
      
		$exceptAdmin=[];
        $allowVisitors=[];

        $this->setMiddleware( $exceptAdmin, $allowVisitors);

       
        $this->identities=$identities;

        $this->setCheckAdmin($checkAdmin);
	}
    public function index()
    {
        if(!request()->ajax()){
            $menus=$this->menus($this->key);            
            return view('discounts.identity')
                    ->with(['menus' => $menus]);
        }  
        $identityList=Identity::orderBy('member')->get();
      
        if(!empty($identityList)){
            $current_user=$this->currentUser();
            foreach ($identityList as $identity) {
                $identity->canDelete = $identity->canDeleteBy($current_user);
                $identity->canEdit = $identity->canEditBy($current_user);
            }
        }
       
        return response()->json(['identityList' => $identityList ]);
           
    }
    public function create()
    {
        $identity=Identity::initialize();
      
        return response()->json([
            'identity' => $identity,
        ]);
    }
    public function store(IdentityRequest $request)
    {
        $values=$request->getValues();
       
        $identity=Identity::create($values);
        return response()->json($identity);
    }
    public function edit($id)
    {
        $current_user=$this->currentUser();
        $identity=Identity::findOrFail($id); 
        if(!$identity->canEditBy($current_user)){
            return  $this->unauthorized();    
        }
      
        return response()->json([
            'identity' => $identity,

        ]);
        
    }
    public function update(IdentityRequest $request, $id)
    {
        $current_user=$this->currentUser();
        $identity=Identity::findOrFail($id); 
        if(!$identity->canEditBy($current_user)){
            return  $this->unauthorized();    
        }

         
        $values=$request->getValues();
        $identity->update($values);

        return response()->json($identity);
    }

    public function destroy($id)
    {
        $identity=Identity::findOrFail($id); 
       
        $current_user=$this->currentUser();
         if(!$identity->canDeleteBy($current_user)){
             return  $this->unauthorized();      
        }

        $identity->delete();

        return response()->json([ 'deleted' => true ]);
    }

    public function options()
    {
        $options=$this->identities->options();
        return response()
            ->json([
                'options' => $options
            ]);
    }
}
