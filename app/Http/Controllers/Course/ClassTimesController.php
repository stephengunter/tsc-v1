<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\BaseController;

use Illuminate\Http\Request;
use App\ClassTime;
use App\Course;
use App\Weekday;
use App\Http\Requests\Course\ClassTimeRequest;
use App\Repositories\Classtimes;
use App\Repositories\Weekdays;


use App\Support\Helper;
use App\Http\Middleware\CheckAdmin;

class ClassTimesController extends BaseController
{
    public function __construct(Classtimes $classtimes,Weekdays $weekdays,CheckAdmin $checkAdmin) 
    {
        $exceptAdmin=[];
        $allowVisitors=[];
        $this->setMiddleware( $exceptAdmin, $allowVisitors);
        

		$this->classtimes=$classtimes;
        $this->weekdays=$weekdays;

        $this->setCheckAdmin($checkAdmin);
	}

    public function index()
    {
        $request = request();
        $course_id=(int)$request->course; 

        $classtimeList=$this->classtimes->getByCourse($course_id);
        if(count($classtimeList)){
            $classtimeList=$classtimeList->orderBy('weekday_id')
                                         ->orderBy('on')->get();
            $current_user=$this->currentUser();
            foreach ($classtimeList as $classtime) {
                 $classtime->canEdit=$classtime->canEditBy($current_user);
                 $classtime->canDelete=$classtime->canDeleteBy($current_user);
            }
        }
         
        return response()
                    ->json([
                        'classtimeList' => $classtimeList
                    ]);
          
    }
   
    public function create()
    {
        $request = request();
        $course_id=(int)$request->course; 

        $course=Course::findOrFail($course_id);
        $current_user=$this->currentUser();
        if(!$course->canEditBy($current_user)){
            return  $this->unauthorized();  
        }

        $weekdayOptions=$this->weekdays->options(); 
        $weekday_id=$weekdayOptions[0]['value'];
        $classtime= ClassTime::initialize($course_id,$weekday_id);
        return response()
            ->json([
                'classtime' => $classtime,
                'weekdayOptions' => $weekdayOptions,
            ]);
    }
    public function store(ClassTimeRequest $request)
    {
        $current_user=$this->currentUser();
        $removed=false;
        $updated_by=$current_user->id;
        $values=$request->getValues($updated_by,$removed);

        $course_id=$values['course_id']; 
        $course=Course::findOrFail($course_id);
        if(!$course->canEditBy($current_user)){
             return  $this->unauthorized();      
        }
       
        $classtime = ClassTime::create($values);
       
        return response()->json($classtime);
                
      
    }
    public function show($id)
    {
          
        $classTime=ClassTime::with('weekday')->findOrFail($id);
         return response()
                ->json([
                    'classTime' => $classTime
                ]);
       
    }
    public function edit($id)
    {
         $classtime=ClassTime::findOrFail($id);
         $current_user=$this->currentUser();
        
         if(!$classtime->canEditBy($current_user)){
             return  $this->unauthorized();
         }
         
         $weekdayOptions=$this->weekdays->options();    
         return response()
                ->json([
                     'classtime' => $classtime,
                     'weekdayOptions' => $weekdayOptions,
                ]);        
    }
    public function update(ClassTimeRequest $request, $id)
    {
         $classtime=ClassTime::findOrFail($id);
         $current_user=$this->currentUser();
        
         if(!$classtime->canEditBy($current_user)){
            return  $this->unauthorized(); 
         }

         $removed=false;
         $updated_by=$current_user->id;
         $values=$request->getValues($updated_by,$removed);
       
         $classtime->update($values);

         return response()->json($classtime);
    }
    public function destroy($id)
    {
       $classtime = ClassTime::findOrFail($id);
       $current_user=$this->currentUser();       
       if(!$classtime->canDeleteBy($current_user)){
            return  $this->unauthorized();  
       }

       $classtime->delete();

       return response() ->json(['deleted' => true ]);
       
    }


    
   
}
