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
        $city=City::select('name')->find($this->city_id)->name;
        $district=District::select('name')->find($this->district_id)->name;

        return $city . $district . $this->streetAddress;
    }

    public static function initializeByZipcode($zipcode,$street,$updated_by)
    {
        $district=District::where('zipcode', $zipcode)->first();
        if(!$district) return null;

      
        return [            
			 'city_id' => $district->city->id,
			 'district_id' => $district->id,
		     'zipcode' =>  $zipcode,
             'streetAddress' => $street,
             'updated_by' => $updated_by
        ];

       
    }

    public static function createByZipcode($zipcode,$street,$updated_by)
    {
        $values=static::initializeByZipcode($zipcode,$street,$updated_by);
        if(!$values) return null;

        return static::create($values);
    }

    public function updateByZipcode($zipcode,$street,$updated_by)
    {
        $values=static::initializeByZipcode($zipcode,$street,$updated_by);
        if($values){
            $this->update($values);
        } 
    }
}
