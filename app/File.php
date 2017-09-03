<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Storage;

class File extends Model
{
    protected $fillable = ['title', 'description', 'path', 'mime', 'user_id'	];
    

    public function storagePath()
    {
       $folder = Storage::disk('upload_files')->getDriver()->getAdapter()->getPathPrefix();
       
       return $folder . $this->path; 
    }
}
