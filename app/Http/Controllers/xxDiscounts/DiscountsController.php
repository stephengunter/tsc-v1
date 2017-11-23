<?php

namespace App\Http\Controllers\Discounts;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Http\Requests\Discounts\DiscountRequest;

use App\Discount;

use App\Repositories\Discounts;
use App\Repositories\Identities;

use App\Http\Middleware\CheckAdmin;
use App\Support\Helper;

class DiscountsController extends BaseController
{
    
    protected $key='signups';

    public function __construct(Discounts $discounts)
    {

        $this->discounts=$discounts;
        $this->identities=$identities;
	}

    public function index()
    {
        if(!request()->ajax()){
            $menus=$this->menus($this->key);            
            return view('discounts.index')
                    ->with(['menus' => $menus]);
        }  

        $discountList=$this->discounts->getAll()
                                ->orderBy('active','desc')->orderBy('points')
                                ->get();
        // if(count($discountList)){
        //     $current_user=$this->currentUser();
        //     foreach ($discountList as $discount) {
        //         $discount->canDelete = $discount->canDeleteBy($current_user);
        //         $discount->canEdit = $discount->canEditBy($current_user);
        //         $discount->identity = $discount->identity();
        //     }
        // }
         
        return response()->json([ 'discountList' => $discountList]);            
                    
    }

    public function create()
    {
        $discount=Discount::initialize();
        
        $identityOptions= $this->identities->options();
      
        return response()->json([
            'discount' => $discount,
            'identityOptions' => $identityOptions

        ]);
    }

    public function store(DiscountRequest $request)
    {
        $current_user=$this->currentUser();
        $updated_by=$current_user->id;
        $removed=false;
        $values=$request->getValues($updated_by,$removed);

        $discount= Discount::create($values);
        return response() ->json($discount);
    }
    public function edit($id)
    {
        $current_user=$this->currentUser();
        $discount=$this->discounts->findOrFail($id);
        if(!$discount->canEditBy($current_user)){
            return  $this->unauthorized();    
        }
        $identityOptions= $this->identities->options();
      
        return response()->json([
            'discount' => $discount,
            'identityOptions' => $identityOptions

        ]);
        
    }
    public function update(DiscountRequest $request, $id)
    {
        $current_user=$this->currentUser();
        $discount=$this->discounts->findOrFail($id);
        if(!$discount->canEditBy($current_user)){
            return  $this->unauthorized();    
        }

        $updated_by=$current_user->id;
        $removed=false;
        $values=$request->getValues($updated_by,$removed);

        $discount->update($values);

        return response()->json(['discount' => $discount   ]);    
          
    }

    public function destroy($id)
    {
       $discount=$this->discounts->findOrFail($id); 
       $current_user=$this->currentUser();
       if(!$discount->canDeleteBy($current_user)){
            return  $this->unauthorized();      
       }
       
       $this->discounts->delete($id, $current_user->id);

        return response()->json([ 'deleted' => true ]);
          
               
           
    }

    public function activeDiscounts()
    {
        $discountList=$this->discounts->activeDiscounts()->get();
         return response()
            ->json([
                'discountList' => $discountList
            ]);
    }



   



}
