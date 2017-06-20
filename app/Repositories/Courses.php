<?php

namespace App\Repositories;

use App\Course;
use App\Status;
use App\Category;
use App\Center;
use App\Teacher;
use App\Term;
use Carbon\Carbon;

use DB;

class Courses 
{
    public function getAll()
    {
         return Course::where('removed',false);
    }
    public function findOrFail($id)
    {
         return Course::findOrFail($id);
    }
    public function getByTeacher($teacher_id)
    {
          return  $this->getAll()->whereHas('teachers', function($q)  use ($teacher_id)
                                    {
                                        $q->where('user_id', $teacher_id );
                                    });


    }
    public function activeCourses()
    {
        $courseList=$this->getAll();
        return $courseList->where('active',true);
    }
    public function getActiveCourses($categoryId)
    {
        $courseList=$this->getAll()->with(['center','categories','teachers','classTimes']);
        if($centerId) $courseList=$courseList->where('center_id',$centerId);
        if($categoryId){
             $courseList= $courseList->whereHas('categories', function($q)  use ($categoryId)
             {
                $q->where('id', $categoryId );
             });
        }
        return $courseList;
        
    }
    public function index($termId,$categoryId,$centerId,$weekdayId)
    {
        $courseList=$this->getAll()->with(['center','categories','teachers','classTimes']);
        
        if($termId) $courseList=$courseList->where('term_id',$termId);       

        if($centerId) $courseList=$courseList->where('center_id',$centerId);

        if($categoryId){
             $courseList= $courseList->whereHas('categories', function($q)  use ($categoryId)
             {
                $q->where('id', $categoryId );
             });
        }

        if($weekdayId){
            $courseList= $courseList->whereHas('classTimes', function($q) use ($weekdayId)
            {
                $q->where('weekday_id', $weekdayId);
            });

        }

        return $courseList;
    }
    public function searchByName($name)
    {
        $courseList=$this->getAll();
        if($name) $courseList=$courseList->where('name','LIKE',"%{$name}%");

        return $courseList;       
    }
    public function searchByCenter(int $center_id ,int $term_id=0)
    {
        $courseList=$this->getAll();
        if($center_id > 0) $courseList=$courseList->where('center_id',$center_id);        
        if($term_id > 0) $courseList=$courseList->where('term_id',$term_id);     

         return $courseList;       
    }
    public function courseNotInCategory($termId,$categoryId,$centerId,$active)
    {
        $courseList=$this->getAll()->with(['center','categories','teachers','classTimes']);
        
        $courseList=$courseList->where('active',$active)->where('term_id',$termId);       

        if($centerId) $courseList=$courseList->where('center_id',$centerId);

        $courseList= $courseList->whereDoesntHave('categories', function($q)  use ($categoryId)
        {
            $q->where('id', $categoryId );
        });

       

        return $courseList;
    }
    
   
     public function store($courseValues , $categoryIds, $teacherIds)
     {
        $term_id=$courseValues['term_id'];
        $number=$this->generateNumber($term_id); 
        $course= DB::transaction(function() 
        use($courseValues,$number){
              $course=new Course($courseValues);
              $course->number=$number;
              $course->save();

              $statusValues=Status::initialize($course);
              $status=new Status($statusValues);
              $course->status()->save($status);

              return $course;
              
        });
        
        $this->syncCategories($categoryIds , $course);
        
        $this->syncTeachers($teacherIds , $course);

        return $course;
     }
     public function update($courseValues , $categoryIds, $teacherIds, $id)
     {
         $course=Course::findOrFail($id); 
         
         $course->update($courseValues);

         $this->syncCategories($categoryIds , $course);    
         $this->syncTeachers($teacherIds , $course);

         return $course;
     }

   

    private function syncCategories($categoryIds , $course)
    {
        $course->categories()->sync($categoryIds);
    }
    private function syncTeachers($teacherIds , $course)
    {
        $course->teachers()->sync($teacherIds);
    }
    

     public static function generateNumber($termid)
     {
        $term=Term::find($termid);
        $count=$term->courses->count();
        $count+=1;

        $countString='';
         if($count < 10){
            $countString= "00" . $count;
         } else if($count<100){
             $countString= "0" . $count;
         }else{
             $countString=$count;
         }
         
         return $term->number .  $countString;
     }
    public function optionsConverting($courseList)
    {
        $options=[];
        foreach($courseList as $course)
        {
            $item=[ 'text' => $course->number . ' ' . $course->name , 
                     'value' => $course->id , 
                 ];
            array_push($options,  $item);
        }

        return $options;
    }


    public function delete($id,$admin_id)
    {
        $course = Course::findOrFail($id);
       
         $values=[
            'active' =>0,
            'removed' => 1,
            'updated_by' => $admin_id
         ];
        
         $course->update($values);
        
    }

    public function canSignupCourses()
    {
         $today=Carbon::today()->toDateString();
         return $this->getAll()->whereDate('open_date','<=' , $today)
                                ->whereDate('close_date','>=' , $today);
    }
    
}