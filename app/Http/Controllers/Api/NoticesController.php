<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;

use App\Notice;
use App\Course;
use App\Repositories\Notices;

use App\Support\Helper;
use Carbon\Carbon;

class NoticesController extends BaseController
{
  
    public function __construct(Notices $notices)                                          
    {
         $this->notices=$notices;

	}
    
    public function index()
    {
        $request = request();
        $per_page=(int)$request->per_page; 
        $model=$this->getNotices()->paginate($per_page);

        return response() ->json(['model' => $model  ]); 
       
    }

    
    public function latest()
    {
        $request = request();
        $count=(int)$request->count; 
        if(!$count) $count=8;
        $model=$this->getNotices()->take($count)->paginate(15);

        return response() ->json(['model' => $model  ]);
       
    }
    
    public function show($id)
    {
        $notice=$this->notices->findOrFail($id);
        if($notice->public && $notice->active && !$notice->removed){
             return response()->json(['notice' => $notice]);
        }else{
            abort(404);
        }                  
       
    }

    private function getNotices()
    {
        $notices=$this->notices->activeNotices()
                                ->where('public',true)
                                ->orderBy('date','desc');
        
        return $notices; 
    }
    
    
    
    
    
    
    
}
