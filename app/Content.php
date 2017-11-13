<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $fillable =  [
                                'key',  'title', 'text', 'order',   
                                'active','removed' , 'updated_by'
                            ];
}
