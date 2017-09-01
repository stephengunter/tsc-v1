<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    protected $fillable = [ 
        'notice_id','title' , 'content',      
        'from', 'receivers' , 'attachments','updated_by'                           
      ];
}
