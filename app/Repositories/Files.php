<?php

namespace App\Repositories;

use Carbon\Carbon;
use Storage;
use File;

class Files 
{   
    
    public function __construct()                                          
    {
         $this->disk=Storage::disk('upload_files');

	}
    private function create_file_name($file)
    {
        $timestamp = str_replace([' ', ':','-'], '', Carbon::now()->toDateTimeString());            
        return 'attachment_' .  $timestamp .'.' .$file->getClientOriginalExtension();         
    }
    public function save_temp_file($file) 
    {
        $file_name = $this->create_file_name($file);  
        $path='/temp/' . $file_name;
        $this->disk->put($path,  File::get($file));
     

        return new \App\File([
            'title' => $file->getClientOriginalName(),
            'mime' => $file->getClientMimeType(),
            'path' => $file_name,
           
        ]);
    }
    public function save_upload_file(\App\File $file) 
    {
        $file_path=$file->path;
        $temp_path='temp/' . $file_path;
        $this->disk->move($temp_path, $file_path);
       
	}

    

    
    
}