<?php

namespace App\Http\Controllers\Contact;

use App\Http\Controllers\BaseController;
use App\City;

use Illuminate\Http\Request;

class CitiesController extends BaseController
{
	public function index()
    {
        return response()->json([
                    'cityList' => City::all()
                  ]);
    }
    
	
}
