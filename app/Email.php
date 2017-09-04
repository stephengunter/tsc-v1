<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    protected $fillable = [ 
        'notice_id','title' , 'content',      
        'from', 'receivers' , 'attachments','updated_by'                           
      ];

    public function getAttachments()
    {
          if(!$this->attachments) return null;
  
          $file_ids= explode(',', $this->attachments);
          return \App\File::whereIn('id',$file_ids)->get();
  
    }
}
