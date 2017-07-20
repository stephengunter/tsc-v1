<?php

namespace App\Repositories;

use App\Photo;
use Image;
use Carbon\Carbon;

class Photoes 
{
    private  $default_folder = '/images/uploads/';

    public function create_file_name($file)
    {
        $timestamp = str_replace([' ', ':'], '-', Carbon::now()->toDateTimeString());            
        return $timestamp. '-' .$file->getClientOriginalName();         
    }

    public function save_upload_photo($file ,$width , $height ,$folder_name=null ) 
    {
        if(!$folder_name) $folder_name= $this->default_folder;

        $file_name = $this->create_file_name($file);  
        $file_path= $folder_name . $file_name;

        $save_path =  public_path() . $file_path;

		//尺寸不變
        if(!$width && !$height){
            Image::make($file)->save($save_path);
            return $file_path;
        }

         //鎖定寬度
        if($width && !$height){
             Image::make($file)->resize($width, null, function ($constraint) {
                     $constraint->aspectRatio();
             })-> save($save_path);
            return $file_path;
            
        }
        //鎖定高度
        if(!$width && $height){
             Image::make($file)->resize(null, $height, function ($constraint) {
                     $constraint->aspectRatio();
             })-> save($save_path);
           return $file_path;
            
        }

        //鎖定寬度與高度
        if($width && $height){
             Image::make($file)->resize($width, $height, function ($constraint) {
                     $constraint->aspectRatio();
             })-> save($save_path);

             return $file_path;
        }
	}

     

   
   
   

  
  
   
   
    
}