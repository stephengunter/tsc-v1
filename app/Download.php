<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Storage;

class Download extends Model
{
    protected $fillable = ['title', 'name', 'order', 'type',
    'path','active', 'removed','updated_by'];

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
            'path'=> '',
            
            'removed' => 0,
            'order' => -1,


            'filedata' => '',
			 
        ];
    }

    public function  getStoragePath()
    {
        $storage = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix();
        $folder='downloads/';
       
        return $storage .$folder . $this->path ;
    }

    public function  getUrl()
    {
        return config('app.backend.url') . '/downloads/' . $this->id;
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
