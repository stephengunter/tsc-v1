<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Course;
use App\Term;
use Carbon\Carbon;
use Exception;

class Discount extends Model
{
   protected $fillable = [ 'name', 'center_id', 'all_courses',
							'points_one', 'points_two' ,'ps', 
   						    'removed','active','updated_by'
						  ];
	

    
   public static function initialize()
	{
		return [
			
			'name' => '',
			'all_courses' => 0,
			'center_id' => 0,
			'points_one' => '',
			'points_two' => '',
			'ps' => '',
			'active' => 1,
			
		];
	}

	public static function isStageOne(Term $term,$date=null)
	{
		if(!$date) $date=Carbon::today();
	
		$bird = Carbon::parse($term->bird_date);
		return $date->lte($bird);
	}

	public static function canCreate($user,$center)
	{
		if($user->isDev()) return true;
		if(!$user->isOwner()) return false;
		return $user->admin->canAdminCenter($center);
	} 

   public function canEditBy($user)
	{
		if($user->isDev()) return true;

		if(!$user->isOwner()) return false;

		if($this->center){
			return $user->admin->canAdminCenter($this->center);
		}else{
			return $user->admin->fromHeadCenter();
		}
		
		
          
	}
	public function getFormattedPoints($points)
	{
		if(!$points) return '';
		$points=str_replace('0','',(string)$points);
		
		if($points) $points .= ' 折';

		return $points;
	}

	public function getNameWithDiscount()
	{
		$name=$this->name . $this->ps;
		if(!$this->points)  return $name;
		 
		return $name . ' ' .$this->getFormattedPoints($this->points);
          
	}
	


}
