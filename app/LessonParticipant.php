<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LessonParticipant extends Model
{
    protected $guarded = [];
    
    public function lesson()
    {
          return $this->belongsTo('App\Lesson');
    }
}
