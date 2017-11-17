<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;

use App\Download;
use App\Repositories\Downloads;

use App\Support\Helper;

class DownloadsController extends BaseController
{
   
    public function __construct(Downloads $downloads) 
    {
       
       $this->downloads=$downloads;
		
	}

    public function index()
    {   
        $order=true;
        $downloads=$this->downloads->activeItems($order)
                                    ->get();

        foreach($downloads as $download){
            $download->url=$download->getUrl();
        }                                    

        return response()->json(['downloads' => $downloads]); 
       
    }
    
    
   
}
