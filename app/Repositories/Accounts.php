<?php

namespace App\Repositories;

use App\Account;
use App\Support\Helper;

class Accounts 
{
    public function getAll()
    {
          return Account::where('removed',false);
    }
   
    public function findOrFail($id)
    {
        return Account::findOrFail($id);
       
    }
    public function getByUser($user_id)
    {
        return Account::where('user_id', $user_id);
           
    }
   
    public function store($course,$discount,$user_id,$updated_by)
    {
         $account=$course->account;
         $cost=$course->cost;
         $points=0;
         if($discount){
            $points=$discount->points;
            $account=$account*$points/100;
         }
         
         $values=[
                'course_id' => $course->id,
                'user_id' => $user_id,
                'account' => $account,
                'cost' => $cost,
                'points' => $points,
                'updated_by' => $updated_by,
                'status' => 0
         ];

         if($discount){
            $values=array_add($values, 'discount', $discount->name);
            $values=array_add($values, 'identity', $discount->identityName());
         }


         $account=Account::create($values);
         return $account;
     }
     public function delete($id , $updated_by)
     {
         $account = $this->findOrFail($id);
         $values=[
            'status' => -1 ,
            'removed' => 1,
            'updated_by' => $updated_by
         ];
        
         $account->update($values);
     }

     public function activeAccounts()
     {
        return $this->getAll()->where('active', true);
     }
     
    public function optionsConverting($accounts)
    {
        $options=[];
        foreach($accounts as $account)
        {
             $item=$account->toOption();
            array_push($options,  $item);
        }
          return $options;
    }
     
   
    
}