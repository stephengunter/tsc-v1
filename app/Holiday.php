<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $fillable = ['date',  'name','updated_by'];

    public static function initialize()
    {
        return [
             'date' => '', 
			 'name' => '',
			 'end_date' => '',
        ];
    }

    public function canEditBy($user)
	{
		return $user->isAdmin();   
	}
	public function canDeleteBy($user)
	{
		
		return $this->canEditBy($user);
	}
}
