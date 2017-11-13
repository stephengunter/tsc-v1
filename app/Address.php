<?php

namespace App;

use App\City;
use App\District;
use App\Address;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    
    protected $fillable = ['city_id', 'district_id', 'zipcode', 
     'streetAddress', 'updated_by'
   ];

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
        $city='';
        if($this->city_id) $city=City::select('name')->find($this->city_id)->name;
        
        $district='';
        if($this->district_id) $district=District::select('name')->find($this->district_id)->name;

        return $city . $district . $this->streetAddress;
    }

    public static function initializeByZipcode($zipcode,$street,$updated_by)
    {
        $city_id=0;
        $district_id=0;

        $district=null;
        if($zipcode) $district=District::where('zipcode', $zipcode)->first();

        if($district){
            $district_id=$district->id;
            $city_id=$district->city->id;
        }

      
        return [            
			 'city_id' => $city_id,
			 'district_id' => $district_id,
		     'zipcode' =>  $zipcode,
             'streetAddress' => $street,
             'updated_by' => $updated_by
        ];

       
    }

    public static function createByZipcode($zipcode,$street,$updated_by)
    {
        $values=static::initializeByZipcode($zipcode,$street,$updated_by);

        return static::create($values);
    }

    public function updateByZipcode($zipcode,$street,$updated_by)
    {
        $values=static::initializeByZipcode($zipcode,$street,$updated_by);
        $this->update($values);
    }
}
