<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Http\Requests\PhotoRequest;

use Carbon\Carbon;
use Illuminate\Support\Facades\Input;

use Image;
use App\Photo;

class PhotoesController extends BaseController
{
    private  $default_folder = '/images/uploads/';
   
    public function __construct()                                
    {
          
	}
    public function show($id)
    {
        $photo = Photo::findOrFail($id);
        return response()
            ->json([
                'photo' => $photo
            ]);
    }
    public function store(Request $form)
    {
        
        if(!$form->hasFile('image_file')){
            return   response()
                        ->json(['image_file' => ['無法取得上傳圖檔'] 
                            ]  ,  422);      
        }

        $file=Input::file('image_file');
      
        $width= $form['width'];
        $height= $form['height'];
      
        $folder_name= $this->default_folder;
        
        $save_path = $this->save_upload_photo($file ,$width , $height, $folder_name);

        $photo=new Photo();
        $photo->fill(Input::all());
       
        $photo->path=$save_path ;

        $photo->save();
       
        return response()->json($photo);

    }

    private function create_file_name($file)
    {
        $timestamp = str_replace([' ', ':'], '-', Carbon::now()->toDateTimeString());            
        return $timestamp. '-' .$file->getClientOriginalName();         
    }

    private function save_upload_photo($file ,$width , $height ,$folder_name=null ) 
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
