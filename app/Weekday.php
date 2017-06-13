<?php

namespace App;
use App\ClassTime;
use Illuminate\Database\Eloquent\Model;

class Weekday extends Model
{
    public $timestamps = false;
    
    

    public function classTimes()
    {
        return $this->hasMany(ClassTime::class);
    }
}
