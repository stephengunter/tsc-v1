<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Support\FilterPaginateOrder;

class Notice extends Model
{
    use FilterPaginateOrder;
    protected $filter =  [  'title' , 'content', 'active', 'date' ,      
                            'public', 'emails', 'created_at'
                         ];

    protected $fillable = [ 
                            'title' , 'content', 'active', 'emails',      
                            'public', 'date' , 'attachments',
                            'removed','updated_by'                           
                          ];

    public static function initialize()
    {
        return [
            'date' => '',
            'title' => '',
            'content' => '',
            'active' => 0,
            'public' => 1,
            'emails' => 0,
            'attachments' => '',
            'courses' => ''
        ];
    }
    public function courses()
    {
        return $this->belongsToMany('App\Course','course_notice');
    }

    public function canEditBy($user)
	{
        if($user->isDev()) return true;
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

    public function courseNames()
    {
        $names=[];
        foreach ($this->courses as $course) {
           array_push($names, $course->fullName());
        }
        return $names;
    }
    public function getAttachments()
    {
        if(!$this->attachments) return null;

        $file_ids= explode(',', $this->attachments);
        return \App\File::whereIn('id',$file_ids)->get();

    }
}
