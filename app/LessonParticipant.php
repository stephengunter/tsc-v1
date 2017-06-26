<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Support\FilterPaginateOrder;

class LessonParticipant extends Model
{
    use FilterPaginateOrder;

    protected $fillable = ['lesson_id', 'user_id', 'status', 
						    'role', 'ps','updated_by'
						  ];

    protected $filter = [
        'role', 
    ];
    
    public function lesson()
    {
          return $this->belongsTo('App\Lesson');
    }
}
