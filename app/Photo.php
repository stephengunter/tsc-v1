<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $table='photoes';

    protected $fillable = ['title', 'description', 'path', 'user_id'	];

    public function user()
    {
	    return $this->belongsTo('App\User');
	}

    public static function defaultCourse()
    {
         $photo= new Photo();
         $photo->path='/images/default-course.png';
         $photo->default=true;
         return  $photo;
    }
    public static function defaultCenter()
    {
         $photo= new Photo();
         $photo->path='/images/default-center.png';
         $photo->default=true;
         return  $photo;
    }
    public static function defaultProfile()
    {
         $photo= new Photo();
         $photo->path='/images/default-profile.png';
         $photo->default=true;
         return  $photo;
    }


    
}
