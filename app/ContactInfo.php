<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Address;
use App\User;
use App\Center;

class ContactInfo extends Model {
	
	protected $fillable = [ 'tel', 'fax', 'residenceAddress',
							'contactAddress','updated_by'
						  ];
	
	protected $table = 'contactinfoes';

	public static function initialize()
    {
        return [
             'tel' => '', 
			 'fax' => '',
			 'residenceAddress' => 0,
		     'contactAddress' =>  0
        ];
    }

	public function addressA($with_full_text=true)
	{
		if(!$this->contactAddress) return null;
		$address=Address::find($this->contactAddress);
		if(!$address) return null;

		if($with_full_text)  $address['fullText'] = $address->fullText();
		
		return $address;

	}
	public function addressB($with_full_text=true)
	{
		if(!$this->residenceAddress) return null;
		$address=Address::find($this->residenceAddress);	
		if(!$address) return null;
		if($with_full_text)  $address['fullText'] = $address->fullText();	
		return $address;
	}

	public function updateAddress($zipcode, $street , $updated_by)
	{
		$addressA=$this->addressA(false);
		if($addressA){
			$addressA->updateByZipcode($zipcode,$street,$updated_by);
			$this->updated_by=$updated_by;
			$this->save();
		}else{
			$address=Address::createByZipcode($zipcode,$street,$updated_by);
			if($address) {
				$this->contactAddress=$address->id;
				$this->updated_by=$updated_by;
				$this->save();
			}
		}
	}
	public static function createByAddress($zipcode, $street, $updated_by)
	{
		$address=Address::createByZipcode($zipcode,$street,$updated_by);
		if($address){
			$values=static::initialize();
			$values['contactAddress']=$address->id;
			return static::create($values);
		}else{
			return null;
		}
		
	}

	public function getUser()
	{
		if(!$this->id) return null;
		return User::where('contact_info',$this->id)->first();
	}

	public function getCenter()
	{
		if(!$this->id) return null;
		return Center::where('contact_info',$this->id)->first();
	}
	public function canViewBy($user)
	{
		
		if(!$user) return false;
		if($this->getUser()){
			return $this->getUser()->canViewBy($user);
		}
		
		return true;
	}

	public function canEditBy($user)
	{
		if(!$user) return false;
		if($this->getUser()){
			return $this->getUser()->canEditBy($user);
		}
		if($this->getCenter()){
			return $this->getCenter()->canEditBy($user);
		}

		return true;
          
	}
	
}
