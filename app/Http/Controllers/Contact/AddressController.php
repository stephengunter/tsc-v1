<?php

namespace App\Http\Controllers\Contact;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Address;
use App\City;
use App\District;
use App\Http\Requests\Contact\AddressRequest;

use App\Http\Middleware\CheckAdmin;


class AddressController extends BaseController
{
    public function __construct(CheckAdmin $checkAdmin)
    {
        
          $exceptAdmin=[];
          $allowVisitors=[];

          $this->setMiddleware( $exceptAdmin, $allowVisitors);
        
          $this->setCheckAdmin($checkAdmin);
          
	}
    public function create()
    {
        
        $address= Address::initialize();

        $cities=City::all();
        $districts=$cities[0]->districts;
        $address['city_id']=$cities[0]->id;
        return response()
            ->json([
                'address' => $address,
                'cities' =>  $cities,
                 'districts' =>  $districts
            ]);
    }
    public function store(AddressRequest $request)
    {
        $address=Address::create($request['address']);
        return response()->json($address);
      
    }
    public function edit($id)
    {
        $address = Address::findOrFail($id);
        $cities=City::all();
        $districts=District::where('city_id', $address->city_id)->get();
         return response()
            ->json([
                'address' => $address,
                'cities' =>  $cities,
                 'districts' =>  $districts
            ]);
        
    }

    public function update(AddressRequest $request, $id)
    {
         $address = Address::findOrFail($id);

         $address->update($request['address']);

           return response()->json($address);
    }

    public function destroy($id)
    {
       $address = Address::findOrFail($id);

       $address->delete();

        return response()
            ->json([
                'deleted' => true
            ]);
    }


    public function show($id)
    {
        $address=Address::findOrFail($id);
        return response()
            ->json([
                'address' => $address
            ]);
    }
    
    
}
