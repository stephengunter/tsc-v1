<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    protected $fillable = ['title', 'name', 'order', 'type',
    'filedata','active', 'removed','updated_by'];

    public static function  canEdit($user)
    {
        return \App\Center::canEdit($user);
    }

    public static function initialize()
    {
        return [
            'title' => '',
            'name' => '',
            'type' => '',
            'active' => 0,
            'filedata' => '',
            'removed' => 0,
            'order' => -1
			 
        ];
    }
    
    public function canEditBy($user)
	{
		return 	static::canEdit($user);
          
	} 
	public function canDeleteBy($user)
	{
		
		if($this->active) return false;
		return $this->canEditBy($user);
	}
}
