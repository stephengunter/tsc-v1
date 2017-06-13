<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $guarded = [];

    public function toOption()
    {
        $text=$this->bank . $this->branch . ' ' . $this->number . ' ' .$this->owner;
        $item=[ 
                 'text' => $text, 
                  'value' => $this->id , 
             ];

         return $item;
    }
}
