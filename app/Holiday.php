<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $fillable = ['date',  'name','updated_by'];
	public static function canEdit($user)
	{
		if(!$user->isAdmin()) return false;
		return $user->admin->fromHeadCenter();
	}
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
		return static::canEdit($user);
	}
	public function canDeleteBy($user)
	{
		
		return $this->canEditBy($user);
	}
}
