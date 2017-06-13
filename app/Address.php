<?php

namespace App;

use App\City;
use App\District;
use App\Address;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    
   protected $guarded = [];

    public static function initialize()
    {
        return [
            
			 'city_id' => '',
			 'district_id' => '',
		     'zipcode' =>  '',
             'streetAddress' => ''
        ];
    }

    public function fullText()
    {
        $city=City::select('name')->find($this->city_id)->name;
        $district=District::select('name')->find($this->district_id)->name;

        return $city . $district . $this->streetAddress;
    }
}
