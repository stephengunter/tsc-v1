<?php

namespace App\Repositories;

use Carbon\Carbon;
use Storage;
use File;

class Files 
{
    private function create_file_name($file)
    {
        $timestamp = str_replace([' ', ':','-'], '', Carbon::now()->toDateTimeString());            
        return 'attachment_' .  $timestamp .'.' .$file->getClientOriginalExtension();         
    }
    public function save_temp_file($file) 
    {
        $file_name = $this->create_file_name($file);  
        $path='/temp/' . $file_name;
        Storage::disk('upload_files')->put($path,  File::get($file));
     

        return new \App\File([
            'title' => $file->getClientOriginalName(),
            'mime' => $file->getClientMimeType(),
            'path' => $file_name,
           
        ]);
    }
    public function save_upload_file(File $file) 
    {
        

       
	}

    

    
    
}