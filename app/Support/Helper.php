<?php

namespace App\Support;

use App\Center;

class Helper 
{
    public static function getAppSettings($frontend=false)
    {
       if($frontend){
          return config('app.frontend');
       }else{
          return config('app.backend');
       }
    }
    public static function buildUrl($spa,$url,$action,$query)
    {
         if($spa){
              $url= $url .'/#' ;
         }
         if($action){
             $url= $url .'/' . $action;
         }
         if($query){
             $url=  $url . "?" . http_build_query($query);
         }

         return $url;
    }
    
   
    public static function setUpdatedBy($values, $user_id)
    {
        if (array_key_exists('updated_by', $values)) {
             $values['updated_by']=$user_id;
        }else{
           $values=array_add($values, 'updated_by', $user_id);
        }

        return $values;
    }
    public static function setRemoved($values, $removed)
    {
        if (array_key_exists('removed', $values)) {
             $values['removed']=$removed;
        }else{
           $values=array_add($values, 'removed', $removed);
        }

        return $values;
    }
    
    public static function checkId($user, $id)
    {
        if(!$user) return false;
       return ( intval($id) == intval($user->id) );
    }

    public static function centersIntersect($entityA, $entityB)    
    {
        $array1 = $entityA->centers()->pluck('id')->toArray();
		$array2 = $entityB->centers()->pluck('id')->toArray();
        return count(array_intersect($array1, $array2));
    }

    public static function validCenters($entity)
    {
       if(!$entity->centers()->count()) return null;
       return $entity->centers()->where('removed',false)->get();

    }

    public static function centersCanAdd($entity)
    {
        if($entity->centers()->count()){
			
            $centerIds =$entity->centers()->pluck('id');
			return Center::where('removed',false)->whereNotIn('id' , $centerIds)->get();
		

		}else{
          	return Center::where('removed',false)->get();
		}

    }
    public static function centersCanAddByAdmin($entity,$admin)
	{
        $centerIds=[];
        if($entity->centers()->count()){
			 $centerIds = Center::where('removed',false)
                            ->whereNotIn('id' , $entity->centers()->pluck('id'))
                             ->pluck('id')->toArray();

		}else{
          	$centerIds= Center::where('removed',false)->pluck('id')->toArray();
		}

        
		
        $canAddIds= array_intersect($centerIds, $admin->centers()->pluck('id')->toArray());
        if(!count($canAddIds)) return null;
        return Center::where('removed',false)->whereIn('id' , $canAddIds)->get();
	}
    public static function getHourMinute($val)
    {
        $value=(string)$val;
        if(strlen($value) < 4 ){
            $value='0' . $value;         
        }
        $hour=substr($value, 0, 2);
        $minute=substr($value, 2, 2);
        return [
            'hour' => (int)$hour,
            'minute' => (int)$minute
        ];
    }
    
    

   

}
