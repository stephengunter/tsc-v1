<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use App\Support\FilterPaginateOrder;
use Illuminate\Notifications\Notifiable;

class Resume extends Model
{
    use FilterPaginateOrder;
    use Notifiable;

    protected $primaryKey = 'user_id';
    protected $guarded = [];

    protected $filter = [
        'user.profile.fullname','specialty', 'active','reviewed', 'education','updated_at'
    ];

    public function user() {
        return $this->belongsTo('App\User');
    }
    public function getPhoto()
    {
        return $this->user->profile->photo();
    }

    public static function initialize()
    {
        return [
            'experiences' => '',
            'certificate' => '',
            'specialty' => '',
            'active' => 0,
			'reviewed' => 0,
			'education' => '',
            'job' => '',
			'jobtitle' => '',
        ];
    }
    public function canViewBy($user)
	{
		if($user->id==$this->user_id) return true;
		return $user->isAdmin();
	}
	
	public function canEditBy($user)
	{
		if($user->id==$this->user_id) return true;

        return $user->isAdmin();
          
	}
	public function canDeleteBy($user)
	{
        if($user->id==$this->user_id) return true;
        return false;
	}
}
