<?php

namespace App\Repositories;

use App\Download;


class Downloads 
{
    public function getAll()
    {
         return Download::where('removed',false);
    }
}