<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Discount;

class Identity extends Model
{
    protected $fillable = ['name','member','ps'];
    
    public static function initialize()
	{
		return [
			'name'=>'',
			'member' => 0,
			'ps' => '',
			
		];
	}

    public function discounts() 
	{
        return Discount::where('identity_id',$this->id);
	}

    public function validDiscounts()
	{
        return $this->discounts()->where('removed',false);
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
        if($this->validDiscounts()->count()) return false;
        return true;
    }

     
}
