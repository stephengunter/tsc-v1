<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Bill;
use App\Refund;

use App\Support\FilterPaginateOrder;
use Carbon\Carbon;

class Tuition extends Model
{
    use FilterPaginateOrder;

    protected $fillable = [
                            'bill_id', 'date', 'amount', 
                            'refund_id', 'refund',
                            'pay_by', 'updated_by', 'ps'
                        // 'bank_branch', 'account_owner','account_number',
                          ];

    protected $filter =  ['date'];

                                
    
    
    public static function initialize(Bill $bill=null)
    {
        
        return [            
            
            'bill_id' => $bill ? $bill->id : '',
            
            'date' => '',
          
            'amount' => $bill ? $bill->amount : '',
            'pay_by' => 0,
            'ps' => ''
        ];
    }  

    public static function initializeRefund(Refund $refund)
    {
        
        return [            
            
            'refund_id' => $bill_id,
            'date' => '',
         
            'amount' => 0,
            'pay_by' => 0,
            'ps' => ''
        ];
    } 
    
    public function isRefund()
    {
        if(!$this->refund_id) return false;

        return (int)$this->refund_id > 0 ;
    }

    public function getBill()
    {
        if(!$this->bill_id) return null;

        return Bill::find($this->bill_id);
    }
    public function getRefund()
    {
        if(!$this->refund_id) return null;

        return Refund::find($this->refund_id);
    }
    

    public function canViewBy($user)
    {
        if($this->isRefund()){
            return $this->getRefund()->canViewBy($user);
        }else{
            return $this->getBill()->canViewBy($user);
        }
       
    }

    public function canEditBy($user)
	{
        if($this->isRefund()){
            return $this->getRefund()->canEditBy($user);
        }else{
            return $this->getBill()->canEditBy($user);
        }
          
  	}
    public function canDeleteBy($user)
	{
		return $this->canEditBy($user);
	}

    
}
