<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    public function courses()
    {
        return $this->belongsToMany('App\Course','course_category');
    }
}
