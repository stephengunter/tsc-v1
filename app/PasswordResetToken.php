<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PasswordResetToken extends Model
{
     protected $table = 'password_reset_tokens';
     protected $primaryKey = 'user_id';

     protected $hidden = [
		 'token'
	];

    protected $guarded = [];

    public function user() {
		return $this->belongsTo('App\User');
	}

    public static function initialize()
    {
        return [
             'token' => hash_hmac('sha256', str_random(40), config('app.key')),
			 'expire_date' => Carbon::now()->addHours(24)
        ];
    }

    public function isValid()
    {
        $expireDate= new Carbon($this->expire_date);
        return $expireDate >= Carbon::now();
    }
}
