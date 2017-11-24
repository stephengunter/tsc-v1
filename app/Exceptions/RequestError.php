<?php

namespace App\Exceptions;

use Exception;

class RequestError extends Exception
{
   public function __construct(array $err)
   {
        $this->err = $err;
   }

   public function getError()
   {
      return $this->err;
   }
    
}
