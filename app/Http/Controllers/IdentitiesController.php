<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\IdentityRequest;

use App\Identity;

use App\Http\Middleware\CheckAdmin;
use App\Support\Helper;

class IdentitiesController extends Controller
{
    public function __construct(CheckAdmin $checkAdmin) {
      
		$this->middleware('admin');

        $this->checkAdmin=$checkAdmin;
	}
    public function index()
    {
        $identityList=Identity::all();
        foreach ($identityList as $identity) {
            $identity->canDelete = $identity->canDelete();
        }
       
        return response()
            ->json([
                'identityList' => $identityList
            ]);
    }
    public function store(IdentityRequest $request)
    {
        $values=$request->getValues();
       
        $identity=Identity::create($values);
        return response()->json($identity);
    }

    public function update(IdentityRequest $request, $id)
    {
         $identity=Identity::findOrFail($id); 
         $values=$request->getValues();
         $identity->update($values);

          return response()->json($identity);
    }

    public function destroy($id)
    {
         $identity=Identity::findOrFail($id); 

         if(!$identity->canDelete()){
           return   response()->json(['msg' => '權限不足' ]  ,  401);    
         }
     
         $identity->delete();

         return response()
            ->json([
                'deleted' => true
            ]);
    }

    public function options()
    {
        $options=[];
        $identityList=Identity::all();
        foreach($identityList as $identity)
        {
            $item=[ 'text' => $identity->name , 
                     'value' => $identity->id , 
                 ];
            array_push($options,  $item);
        }

        return response()
            ->json([
                'options' => $options
            ]);
    }
}
