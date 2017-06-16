<?php

namespace App\Repositories;

use App\Course;
use App\Weekday;
use App\Lesson;
use App\Holiday;
use Carbon\Carbon;
class Lessons 
{
    public function getAll()
    {
         return Lesson::where('removed',false);
    }
    public function getByCourse($course)
    {
         return $this->getAll()->where('course_id', $course);
            
    }
    public function findOrFail($id)
    {
        $lesson=Lesson::findOrFail($id);   
        return $lesson;
    }


     public function initialize($course , $classroom_id, $updated_by)
     {
        $beginDate=Carbon::parse($course->begin_date);

        $params=$this->getParams($course);

        $weekdays=$params['weekdays'];
        $classTimeList=$params['classTimeList'];
      
        if (!in_array($beginDate->dayOfWeek, $weekdays))
        {
             return   response()->json([
                    'begin_date' => ['起始日期與上課時間不符'] 
                      ]  ,  422);   
        }
        
         $weeks=intval($course->weeks);
         $terms=$weeks * count($weekdays);
       
         $allDays=$this->getClassDays($beginDate , $terms , $weekdays);
        
        
        $classDays=$allDays['classdays'];
        $offdays=$allDays['offdays'];
        for ($i = 0; $i < count($offdays); $i++) {
            $offDay=$offdays[$i];
            $values=[
                'course_id' => $course->id,
                'date' => $offDay['date'],
                'status' => -1 ,
                'ps' =>  $offDay['ps'],    
                'updated_by' => $updated_by       
            ];
            Lesson::create($values);
        }

        $withSchedules=true;
        $schedules=$course->schedules;
        if($schedules->count() != count($classDays)){
             $withSchedules=false;
        }

         for ($i = 0; $i < count($classDays); $i++) {
            $classDay=$classDays[$i];
            $dayOfWeek=$classDay['dayOfWeek'];
            $classTime= $classTimeList[$dayOfWeek];

            $values=[
                'course_id' => $course->id,
                'classroom_id' => $classroom_id,
                'order' => $i+1,
                'date' => $classDay['date'],
                'on' => $classTime->on,
                'off' => $classTime->off,
                'updated_by' => $updated_by   
            ];

            if($withSchedules){

                $schedule=$schedules[$i];

                $values['title']= $schedule->title;
                $values['content']= $schedule->content;
                $values['materials']= $schedule->materials;

            }
            $lesson=Lesson::create($values);
            
        }

         
        
     }

     private function getParams($course)
     {
        $classTimes=$course->classTimes;
        if(!$classTimes->count()){
            return   response()->json([
                    'classTimes' => ['缺少上課時間'] 
                      ]  ,  422);   
        }
        $weekdays=[];
        $classTimeList=[];
        foreach($classTimes as $classTime)
        {
            $weekday=Weekday::findOrFail($classTime->weekday_id);
            array_push($weekdays,  $weekday->val);
            $classTimeList[$weekday->val] = $classTime;
        }

         return [
                'weekdays'=>$weekdays,
                 'classTimeList'=>$classTimeList
            ] ;
     }

      private function getClassDays($beginDate , $terms , $weekdays)
      {
           $days=[];       
           $offDays=[];
           $day=$beginDate;

           while ( count($days) < $terms) {
                for ($d = 1; $d <= 7; $d++) {
                    if(in_array($day->dayOfWeek, $weekdays)){
                        $classDay=[
                            'date' => $day->format('Y-m-d'),
                            'dayOfWeek' => $day->dayOfWeek,
                            'ps'=>''                            
                        ];  
                        $holiday=Holiday::where('date',$day->format('Y-m-d'))->first();
                        if($holiday){
                            $classDay['ps']=$holiday->name;
                            array_push($offDays, $classDay); 
                        }else{
                            array_push($days, $classDay); 
                        }
                    }

                    $day=$day->addDays(1);
                } 
           }
            return [
                'classdays'=>$days,
                'offdays'=>$offDays
            ];
      }

     

     public function index($course)
     {
        $lessonList=$this->getByCourse($course)->get();
        foreach($lessonList as $lesson)
        {
            $lesson->teachers=$lesson->teachers();
            $lesson->volunteers=$lesson->volunteers();
        }

        return $lessonList;
      
       
     }

     public function store($values)
     {
         $lesson=Lesson::create($values); 

         return $lesson;
     }
     public function update($id, $lessonValues)
     {
         $lesson=Lesson::findOrFail($id);     
        
         $lesson->update($lessonValues);

         return $lesson;
     }

     public function dayOff($id, $updated_by)
     {
         $lesson=Lesson::findOrFail($id);
         $offLesson = new Lesson(array_only($lesson->toArray(), ['course_id', 'date']));
         $offLesson->status = -1;
         $offLesson->updated_by=$updated_by;
         $offLesson->save();
        
         $order=$lesson->order;
         $course_id=$lesson->course_id;
         $withClassroom=false;
      
         $afterLessons=$this->getByCourse($course_id,$withClassroom)
                            ->where('order','>=',$order)->get();
         $count=$afterLessons->count();
         for ($i = 0; $i < $count; $i++) {
            $lesson=$afterLessons[$i];
            if($i< $count-1)
            {
                $nextLesson=$afterLessons[$i+1];
                $lesson->date=$nextLesson->date;
                $lesson->updated_by=$updated_by;
                $lesson->save();
            }else{
                 $lesson->date=Carbon::parse($lesson->date)->addWeeks(1);
                 $lesson->updated_by=$updated_by;
                 $lesson->save();
            }
            
         }

         return Lesson::findOrFail($id);
       
     }
    

    public function delete($id,$admin_id)
    {
        $lesson = Lesson::findOrFail($id);
       
         $values=[
           
            'removed' => 1,
            'updated_by' => $admin_id
         ];
        
         $lesson->update($values);
        
    }

     

     

   
   
   

  
  
   
   
    
}