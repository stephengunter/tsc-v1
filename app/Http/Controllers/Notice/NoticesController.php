<?php

namespace App\Http\Controllers\Notice;

use App\Http\Controllers\BaseController;

use Illuminate\Http\Request;
use App\Http\Requests\Notice\NoticeRequest;

use App\Notice;
use App\Repositories\Notices;
use App\Support\Helper;
use App\Http\Middleware\CheckAdmin;

class NoticesController extends BaseController
{
    protected $key='notices';
    public function __construct(Notices $notices,CheckAdmin $checkAdmin)                                          
    {
         $exceptAdmin=[];
         $allowVisitors=[];
		 $this->setMiddleware( $exceptAdmin, $allowVisitors);
		
         $this->notices=$notices;

         $this->setCheckAdmin($checkAdmin);

	}
    public function index()
    {
        if(!request()->ajax()){
            $menus=$this->menus($this->key);            
            return view('notices.index')
                    ->with(['menus' => $menus]);
        }          
        
        $noticeList=$this->notices->getAll()->filterPaginateOrder();
        
        // if(count($courseList)){
        //     foreach ($courseList as $course) {
        //         foreach ($course->classTimes as $classTime) {
        //           $classTime->weekday;
        //         }
        //         foreach ($course->teachers as $teacher) {
        //           $teacher->name=$teacher->getName();
        //         }
        //     }
        // }

        return response() ->json(['model' => $noticeList  ]);  
       
    }
    public function create()
    {
        $request = request();
        if(!$request->ajax()){
            $menus=$this->menus($this->key);            
            return view('notices.create')
                   ->with([ 'menus' => $menus  ]);           
                       
        }  

        $notice=Notice::initialize();

        return response()
            ->json([
                'notice' => $notice
            ]);
         
    }
    public function store(NoticeRequest $request)
    {
        $current_user=$this->currentUser();
        $updated_by=$current_user->id;
        $removed=false;
        $values=$request->getValues($updated_by,$removed);

        $notice= Notice::create($values);
        return response() ->json($notice);
    }
}
