<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\DiscountRequest;

use App\Repositories\Discounts;
use App\Http\Middleware\CheckAdmin;
use App\Support\Helper;

class DiscountsController extends Controller
{
    public function __construct(Discounts $discounts, CheckAdmin $checkAdmin)
    {
		
          $this->middleware('admin', ['except' => ['options','activeDiscounts'] ]);
         

		  $this->discounts=$discounts;

          $this->checkAdmin=$checkAdmin;
	}

    public function index()
    {
        $discountList=$this->discounts->getAll()->get();
         foreach ($discountList as $discount) {
            $discount->canDelete = $discount->canDelete();
            $discount->identity = $discount->identity();
         }
             return response()
                    ->json([
                        'discountList' => $discountList
                    ]);
    }
   
    public function store(DiscountRequest $request)
    {
        $current_user=$this->checkAdmin->getAdmin();
        $updated_by=$current_user->id;
        $removed=false;
        $values=$request->getValues($updated_by,$removed);

        $discount= $this->discounts->store($values);
         return response()
            ->json([
                'discount' => $discount 
            ]);
    }

    public function update(DiscountRequest $request, $id)
    {
        $current_user=$this->checkAdmin->getAdmin();
        $updated_by=$current_user->id;
        $removed=false;
        $values=$request->getValues($updated_by,$removed);

         $discount= $this->discounts->update($values,$id);

           return response()
            ->json([
                'discount' => $discount 
            ]);
    }

    public function destroy($id)
    {
       $discount=$this->discounts->findOrFail($id); 
       $current_user=$this->checkAdmin->getAdmin();
       if(!$discount->canDelete()){
            return   response()->json(['msg' => '權限不足' ]  ,  401);    
       }
       
       $this->discounts->delete($id, $current_user->id);

        return response()
            ->json([
                'deleted' => true
            ]);
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
