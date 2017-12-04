<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;

use App\Content;

use App\Support\Helper;

class ContentsController extends BaseController
{
   
    public function __construct() 
    {
		
	}

    public function index()
    {
        $key=request()->get('key');
        if(!$key) abort(404);

        $contents=Content::where('removed',false)
                            ->where('key',$key)
                            ->where('active',true)
                            ->orderBy('order','desc')
                            ->get();

        return response()->json(['contents' => $contents]); 
       
    }
    
    
   
}
