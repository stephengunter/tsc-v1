<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\User;
use App\Admin;
use App\Support\Helper;

class AdminRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {  
        $rules=[];
        $id=$this->getAdminId();
        if($id){
            //Update
            return $rules;
        }
        
        // With User
        
        $user_id=$this->getUserId();       
        $userValues = $this['user'];
        $email=$userValues['email'];
        $role=Admin::roleName();
      
        $userRules=User::getRules($email, $user_id,$role);
        
        $SID=$this->getSID();
        if($SID){
            $userRules['user.profile.SID']='required|unique:profiles,SID,'.$user_id .',user_id';
        }

        return array_merge($rules,$userRules);
    }

    public function messages()
    {
        $messages=[];

        $userMessages=User::getRuleMessages();
        return array_merge($messages,$userMessages);
    }
    public function getAdminId()
    {
        $id=0;
        $adminValues = $this['admin'];        
        if(array_key_exists ( 'user_id' ,$adminValues)){
            $id=(int)$adminValues['user_id'];
        }
        return $id;
    }
    public function getUserId()
    {
        $userValues = $this['user'];
        $user_id=0;        
        if(array_key_exists ( 'id' ,$userValues)){
            $user_id=(int)$userValues['id'];
        }  
        return $user_id;
    }
    public function getSID()
    {
        $profileValues = $this->getProfileValues();
        $SID='';        
        if(array_key_exists ( 'SID' ,$profileValues)){
            $SID=$profileValues['SID'];
        }  
        return $SID;
    }
    public function getUserValues($updated_by,$removed)
    {
        $request=$this->get('user');
        $values=array_except($request, ['profile']);
        $values=array_except($values, ['contact_info']);
        $values= Helper::setUpdatedBy($values,$updated_by);
        return Helper::setRemoved($values,$removed);
       
    }
    public function getProfileValues($updated_by=null)
    {
        $request=$this->get('user');        
        $values=array_except($request['profile'], ['photo_id']);
        return Helper::setUpdatedBy($values,$updated_by);
        
    }
    public function getAdminValues($updated_by,$removed)
    {
        $values=$this->get('admin');   
        $values=array_except($values, ['user','name']);  
        $values= Helper::setUpdatedBy($values,$updated_by);
        return Helper::setRemoved($values,$removed);
        
    }
}
