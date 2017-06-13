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

	public function addressA()
	{
		if(!$this->contactAddress) return null;
		$address=Address::find($this->contactAddress);
		if(!$address) return null;
		$address['fullText'] = $address->fullText();
		return $address;

	}
	public function addressB()
	{
		if(!$this->residenceAddress) return null;
		$address=Address::find($this->residenceAddress);	
		if(!$address) return null;
		$address['fullText'] = $address->fullText();	
		return $address;
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
