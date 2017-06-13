<?php

namespace App\Http\Controllers\Contact;

use App\Http\Controllers\BaseController;
use App\City;
use App\District;

use Illuminate\Http\Request;

class DistrictsController extends BaseController
{
	public function index()
    {
       
        $city_id=(int)request()->city;
        $city= City::findOrFail($city_id);
         
        return response()->json([
                    'districtList' => $city->districts
                  ]);
            
       
    }
    
	
}
