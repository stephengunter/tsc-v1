<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Support\FilterPaginateOrder;

class LessonParticipant extends Model
{
    use FilterPaginateOrder;

    protected $fillable = ['lesson_id', 'user_id', 
						    'role', 'ps','updated_by'
						  ];

    protected $filter = [
        'role', 
    ];
    
    public function lesson()
    {
          return $this->belongsTo('App\Lesson');
    }

    public function user()
    {
          return $this->belongsTo('App\User');
    }

    public function canEditBy($user)
	{
		return $this->lesson->canEditBy($user);
          
	} 
    public function toOption()
    {
        return [ 'text' => $this->user->profile->fullname , 
                  'value' =>$this->user->id , 
                ];
    }
}
