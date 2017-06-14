<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Identity;

class Discount extends Model
{
    protected $fillable = [ 'key','name', 'identity_id', 
							'points', 'ps', 
   						    'removed','active','updated_by'
						  ];
    
    public static function initialize()
	{
		return [
			'key'=>'',
			'name' => '',
			'identity_id' => '',
			'points' => '',
			'ps' => '',
			'active' => 1,
			
		];
	}

    public function identity() 
	{
        if(!$this->identity_id) return null;

		return Identity::find($this->identity_id);
	}
    public function identityName() 
	{
        $identity=$this->identity();
        if(!$identity) return '';

		return $identity->name;
	}

    public function canEditBy($user)
	{
        return $user->isAdmin();
          
	}
	public function canDeleteBy($user)
	{
		if($this->canDelete()){
			return $this->canEditBy($user);
		}else{
			return false;
		}
		
	}

	public function canDelete()
	{
         return  true;
	}

}
