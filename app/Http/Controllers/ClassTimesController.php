<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ClassTime;
use App\Course;
use App\Weekday;
use App\Http\Requests\Course\ClassTimeRequest;
use App\Repositories\Classtimes;


use App\Support\Helper;
use App\Http\Middleware\CheckAdmin;

class ClassTimesController extends Controller
{
    public function __construct(Classtimes $classtimes,CheckAdmin $checkAdmin) 
    {
         $this->middleware('admin');

		 $this->classtimes=$classtimes;

         $this->checkAdmin=$checkAdmin;
	}

    public function index()
    {
        $request = request();
        $course_id=(int)$request->course; 

        $classTimes=$this->classtimes->getByCourse($course_id);
         
        return response()
                    ->json([
                        'classTimes' => $classTimes
                    ]);
          
    }
   
    public function create()
    {
        $request = request();
        $course_id=(int)$request->course; 

        $course=Course::findOrFail($course_id);
        $current_user=$this->checkAdmin->getAdmin();
        if(!$course->canEditBy($current_user)){
            return   response()->json(['msg' => '權限不足' ]  ,  401);    
        }

        $weekdayOptions=$this->weekdayOptions();
        $weekday_id=$weekdayOptions[0]['value'];
        $classTime= ClassTime::initialize($course_id,$weekday_id);
        return response()
            ->json([
                'classTime' => $classTime,
                'weekdayOptions' => $weekdayOptions,
            ]);
    }
    public function store(ClassTimeRequest $request)
    {
        $current_user=$this->checkAdmin->getAdmin();
        $removed=false;
        $updated_by=$current_user->id;
        $values=$request->getValues($updated_by,$removed);

        $course_id=$values['course_id']; 
        $course=Course::findOrFail($course_id);
        if(!$course->canEditBy($current_user)){
            return   response()->json(['msg' => '權限不足' ]  ,  401);    
        }
       
        
       
        $classTime = ClassTime::create($values);
       
          return response()->json($classTime);
                
      
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
         $classTime=ClassTime::findOrFail($id);
         $current_user=$this->checkAdmin->getAdmin();
        
         if(!$classTime->canEditBy($current_user)){
            return   response()->json(['msg' => '權限不足' ]  ,  401);    
         }
         
         $weekdayOptions=$this->weekdayOptions();    
         return response()
                ->json([
                    'classTime' => $classTime,
                     'weekdayOptions' => $weekdayOptions,
                ]);        
    }
    public function update(ClassTimeRequest $request, $id)
    {
         $classTime=ClassTime::findOrFail($id);
         $current_user=$this->checkAdmin->getAdmin();
        
         if(!$classTime->canEditBy($current_user)){
            return   response()->json(['msg' => '權限不足' ]  ,  401);    
         }

         $removed=false;
         $updated_by=$current_user->id;
         $values=$request->getValues($updated_by,$removed);
       
         $classTime->update($values);
         $classTime->weekday;

         return response()->json($classTime);
    }
    public function destroy($id)
    {
       $classTime = ClassTime::findOrFail($id);
       $current_user=$this->checkAdmin->getAdmin();        
       if(!$classTime->canDeleteBy($current_user)){
            return   response()->json(['msg' => '權限不足' ]  ,  401);    
       }

       $classTime->delete();

        return response()
            ->json([
                'deleted' => true
            ]);
    }


    private function weekdayOptions()
    {
        $weekdayOptions=[];
        $weekdays=Weekday::all();
        foreach($weekdays as $weekday)
        {
            $item=[ 'text' => $weekday->text , 
                     'value' => $weekday->id , 
                 ];
            array_push($weekdayOptions,  $item);
        }

        return $weekdayOptions;
    }
   
}
