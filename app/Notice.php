<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Support\FilterPaginateOrder;

class Notice extends Model
{
    use FilterPaginateOrder;
    protected $filter =  [  'title' , 'content', 'active',       
                            'public',  'courses', 'created_at'
                         ];

    protected $fillable = [ 
                            'title' , 'content', 'active',       
                            'public',  'courses',
                            'removed','updated_by'
                            ];

    public static function initialize()
    {
        return [
            'title' => '',
            'content' => '',
            'active' => 0,
            'public' => 0,
            'courses' => ''
        ];
    }
    public function courses()
    {
        return $this->belongsToMany('App\Course','course_category');
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
