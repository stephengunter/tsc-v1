<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Collection;
use App\Center;
use Carbon\Carbon;

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
             $url=  $url . '?' . http_build_query($query);
         }

         return $url;
    }
    public static function getIntegerByKey(array $arr , $key)
    {
        $val=0;
        if(array_key_exists($key, $arr)) $val=(int)$arr[$key];
        return $val;
    }
    public static function isNullOrEmpty(Collection $value = null)
    {
        if(!$value) return true;
        return $value->isEmpty();
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
    //    if(!$entity->centers()->count()) return null;
       return $entity->centers()->where('removed',false)->get();

    }

    public static function centersCanAdd($entity)
    {
        if($entity->centers()->count()){
			
            $centerIds =$entity->centers()->pluck('id');
            return Center::where('removed',false)->where('oversea',false)
                            ->whereNotIn('id' , $centerIds)->get();
		

		}else{
          	return Center::where('removed',false)->where('oversea',false)->get();
		}

    }
    public static function centersCanAddByUser($entity,$user)
	{
        
        if($user->isAdmin()) return static::centersCanAddByAdmin($entity,$user->admin);

        if(!$user->isDev()) return [];
        
        $centersCanAdd=static::centersCanAdd($entity)->pluck('id')->toArray();        
        
        $admin_center_ids=Center::where('removed',false)->pluck('id')->toArray();
		
        $canAddIds= array_intersect($centersCanAdd, $admin_center_ids);
        
        
        if(!count($canAddIds)) return null;
        return Center::where('removed',false)->whereIn('id' , $canAddIds)->get();
	}
    public static function centersCanAddByAdmin($entity,$admin)
	{
        $centersCanAdd=static::centersCanAdd($entity)->pluck('id')->toArray();        
        
        $admin_center_ids=[];
        $admin_centers=$admin->centersCanAdmin();
        if($admin_centers){
            $admin_center_ids=$admin_centers->pluck('id')->toArray();
        }
		
        $canAddIds= array_intersect($centersCanAdd, $admin_center_ids);
        
        
        if(!count($canAddIds)) return null;
        return Center::where('removed',false)->whereIn('id' , $canAddIds)->get();
	}
    public static function checkDateString($date_string)
    {
        $date=Carbon::parse($date_string);
        if($date->year < 1911){
            return '';
        }else{
            return $date_string;
        }
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
    public static function convertTimeNumberToText($val)
    {
        $time=self::getHourMinute($val);
        $minute='';
        if($time['minute'] == 0 ){
            $minute='00';
        }else{
             $minute=$time['minute'];
        }
        return $time['hour'] . ':' . $minute;
    }
    public static function getFormattedTime($val)
    {
        $value=self::getHourMinute($val);
        $hour=$value['hour'] * 100;
        $minute=$value['minute'];
        return $hour + $minute;
    }
    public static function getTimeNumber($date)
    {
        $date=Carbon::parse($date);
        return $date->hour*100 + $date->minute;
    }

    public static function getWeekdayChineses($date,$formated)
    {
        $date=Carbon::parse($date);
        $weekday=$date->dayOfWeek;
        $text='';
        switch ($weekday) {
            case 0:
                $text = '日';
                break;
            case 1:
                $text = '一';
                break;
            case 2:
                $text = '二';
                break;
            case 3:
                $text = '三';
                break;
            case 4:
                $text = '四';
                break;
            case 5:
                $text = '五';
                break;
            case 6:
                $text = '六';
        }
        if ($formated) return '(' . $text . ')' ;
        return $text;
    }

    public static function formatMoney($val)
    {
       
        $strValue=(String)$val;
        $pos=strpos($strValue, '.',0);
        if(!$pos){
            return (int)$strValue;
        } 

        $cents=(int)substr($strValue , $pos+1 );
        if ($cents > 0) return $strValue;
        return substr($strValue ,0, $pos );

    }

    public static function strFromArray($arrVal,$split=',')
    {
        $str='';
        for($i = 0; $i < count($arrVal); ++$i) {
            if(isset($arrVal[$i])) {
                $str .=  $arrVal[$i] . ',';
            } 
            
        }
        return static::removeLastComma($str);
    }

    public static function removeLastComma($str_val)
    {
          $last=strrpos($str_val, ',');
          return substr($str_val, 0, $last);
    }

    public static function removeValueFromArray(Array $array, $val)
    {
        if(($key = array_search($val, $array)) !== false) {
           
            unset($array[$key]);
            $array = array_values($array);
           
        }

        return $array;
    }
    
    

   

}
