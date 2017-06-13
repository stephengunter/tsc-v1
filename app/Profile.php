<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Photo;

class Profile extends Model {

    protected $primaryKey = 'user_id';
    
	protected $fillable = [
	   'fullname', 'SID', 'gender', 
       'dob' , 'title_id', 'updated_by'
	];

	
    protected $filter = [
        'user_id', 'fullname', 'SID', 'dob', 'gender', 'created_at',
		
        'user.email', 'user.phone', 'user.name',
    ];

	public static function initialize()
    {
        return [            
            'fullname' => '',
            'SID' => '',
            'gender' => 1 ,
            'dob' => '' ,
            'photo_id'=> '',
           
        ];
    }


	public function user() {
		return $this->belongsTo('App\User');
	}

    public function title() {
		return $this->belongsTo('App\Title');
	}
    public function titleText() {
		if($this->title){
            return $this->title->name;
        }else{
            return '';
        }
	}

    public function photo()
	{
		if(!$this->photo_id){
			return Photo::defaultProfile();
		}
		return Photo::find($this->photo_id);

	}

    public function setSIDAttribute($value) {

		$this->attributes['SID'] = strtoupper($value);
	}
	public function toOption()
    {
        $item=[ 
                 'text' => $this->fullname , 
                  'value' => $this->user_id , 
             ];

        return $item;
    }

}
