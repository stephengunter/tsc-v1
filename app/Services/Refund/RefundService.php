<?php

namespace App\Services\Refund;

use App\Repositories\Refunds;


class RefundService
{
   public function __construct(Refunds $refunds)                   
   {
      $this->refunds=$refunds;

   }

   public function statusOptions()
   {
      return $this->refunds->statusOptions();
   }
}