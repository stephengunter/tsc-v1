<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Storage;


use Carbon\Carbon;

class File extends Model
{
    protected $fillable = ['title', 'description', 'path', 'mime', 'user_id'	];
    
    public static function createFileName($file, $type='')
    {
        $timestamp = str_replace([' ', ':','-'], '', Carbon::now()->toDateTimeString());
        $random_file_name= $timestamp .'.' .$file->getClientOriginalExtension(); 
        if($type)   return    $type . '_' .   $random_file_name;    
        else return  $random_file_name;             
    }

    public function storagePath()
    {
       $folder = Storage::disk('upload_files')->getDriver()->getAdapter()->getPathPrefix();
       
       return $folder . $this->path; 
    }
}
