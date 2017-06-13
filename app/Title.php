<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Title extends Model
{
   protected $fillable = ['name'];
   public static function initialize()
   {
        return [
             'name' => '', 
        ];
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
