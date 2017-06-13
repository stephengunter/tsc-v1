<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Discount;

class Identity extends Model
{
    protected $fillable = ['name','member','ps'];

    public function discounts() 
	{
        return Discount::where('identity_id',$this->id);
	}

    public function validDiscounts()
	{
        return $this->discounts()->where('removed',false);
	}

    public function canDelete()
    {
        if($this->validDiscounts()->count()) return false;
        return true;
    }

     
}
