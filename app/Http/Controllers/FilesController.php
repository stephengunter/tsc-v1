<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Http\Requests\PhotoRequest;

use Carbon\Carbon;
use Illuminate\Support\Facades\Input;

use App\Http\Middleware\CheckAdmin;
use App\File;

class FilesController extends BaseController
{
    private  $default_folder = '/files/uploads/';
   
    public function __construct(CheckAdmin $checkAdmin)                                
    {
          $exceptAdmin=['store','show'];
          $allowVisitors=['defaultProfile','defaultCenter','defaultCourse'];

          $this->setMiddleware( $exceptAdmin, $allowVisitors);        

          $this->checkAdmin=$checkAdmin;
          
    }
    public function upload(Request $form)
    {
        if(!$form->hasFile('upload_file')){
            return   response()
                        ->json(['upload_file' => ['無法取得上傳檔案'] 
                            ]  ,  422);      
        }

        $file=Input::file('upload_file');

        $save_path = $this->save_upload_file($file);

        $file=new File([
            'title' => $file->getClientOriginalName(),
            'path' => $save_path,
        ]);

        return response()->json($file);
    }
    public function store(Request $form)
    {
        if(!$form->hasFile('upload_file')){
            return   response()
                        ->json(['upload_file' => ['無法取得上傳檔案'] 
                            ]  ,  422);      
        }

        $file=Input::file('upload_file');

        $save_path = $this->save_upload_file($file);

        $file=new File([
            'title' => $file->getClientOriginalName(),
            'path' => $save_path,
        ]);

        return response()->json($file);
    }
    private function create_file_name($file)
    {
        $timestamp = str_replace([' ', ':','-'], '', Carbon::now()->toDateTimeString());            
        return 'attachment_' .  $timestamp .'.' .$file->getClientOriginalExtension();         
    }
    private function save_upload_file($file,$is_temp=true) 
    {
        $folder_name= $this->default_folder;
        if($is_temp){
            $folder_name .= 'temp/';
        }

        $file_name = $this->create_file_name($file);  
      

        $save_path =  public_path() . $folder_name;
        
        $file->move($save_path, $file_name);
        
        return $save_path . $file_name;

       
	}
}
