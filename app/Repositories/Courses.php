<?php

namespace App\Repositories;

use App\Course;
use App\Status;
use App\Category;
use App\Center;
use App\Teacher;
use App\Profile;


use App\Repositories\Users;
use App\Repositories\Centers;
use App\Repositories\Terms;

use App\Term;
use Carbon\Carbon;

use App\Support\Helper;
use DB;
use Excel;

class Courses 
{
    public function __construct(Users $users, Centers $centers , Terms $terms)                          
    {
        $this->users=$users;  
        $this->centers=$centers; 
        $this->terms=$terms; 
        
    }

    public function getAll()
    {
         return Course::where('removed',false);
    }
    public function getAllGroupCourses()
    {
        return $this->getAll()->where('group', true);
    }
    public function findOrFail($id)
    {
         return Course::findOrFail($id);
    }
    public function findByNumber($number)
    {
          return  $this->getAll()->where('number',$number)->first();

    }
    public function getByTeacher($teacher_id)
    {
          return  $this->getAll()->whereHas('teachers', function($q)  use ($teacher_id)
                                    {
                                        $q->where('user_id', $teacher_id );
                                    });


    }
    public function getByCenter($centerId)
    {
          return  $this->getAll()->where('center_id',$centerId);

    }

    public function groupCategory()
    {
        return Category::groupCategory();
        
    }
    public function parentCourses()
    {
        return $this->getAll()->where('parent',0);
        
    }
    public function subCourses($parent)
    {
        return $this->getAll()->where('parent',$parent);
        
    }

    public function getGroupCourses(int $term_id,int $center_id,$top_only=true)
    {
        $courseList=$this->getAllGroupCourses();
        if($term_id) $courseList=$courseList->where('term_id',$term_id);       
        
        if($center_id) $courseList=$courseList->where('center_id',$center_id);

        if($top_only) $courseList=$courseList->where('parent',0);

        return $courseList;
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
    public function index($termId,$categoryId,$centerId,$weekdayId,$parentId)
    {
        $courseList=$this->getAll()->with(['center','categories','teachers','classTimes']);
        
        if($termId) $courseList->where('term_id',$termId);       

        if($centerId) $courseList->where('center_id',$centerId);

        if($parentId) $courseList->where('parent',$parentId);
        

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
        $parent=(int)$courseValues['parent'];
        $course=new Course($courseValues);
        if($parent){           
            $course->copyParentCourseValues($parent);
        }else{           
            $term=Term::findOrFail($course->term_id);

            $course->open_date=$term->open_date;
            $course->close_date=$term->close_date;
        }

        $course->countTuition();
        
        
         
        $course= DB::transaction(function() 
        use($course){
              
              $course->save();

              $statusValues=Status::initialize($course);
             
              $status=new Status($statusValues);
             
              $course->status()->save($status);

              return $course;
              
        });
        
        if(count($categoryIds)){
            $this->syncCategories($categoryIds , $course);
        }
        if(count($teacherIds)){
            $this->syncTeachers($teacherIds , $course);
        }

        // if($course->isGroup()){
        //     $course->attachGroupCategory();
        // }
       

        return $course;
    }
    public function update($courseValues , $categoryIds, $teacherIds, Course $course)
    {
         $course->update($courseValues);

         $this->syncCategories($categoryIds , $course);    
         $this->syncTeachers($teacherIds , $course);

         return $course;
    }
    public function updateNumber($id,$custom_number,$updated_by)
    {
         $course=$this->findOrFail($id);          
         
         $default_number=$course->generateNumber();
         
         $course->number= $default_number . $custom_number;           
         $course->updated_by= $updated_by;
        
         $course->save();
    
         return $course;
 
    }
    public function updateReview($id,$reviewed,$current_user)
    {
        $course = Course::findOrFail($id);
        if(!$course->canReviewBy($current_user)){
            throw new AuthenticationException();    
        }

        $course->reviewed=$reviewed;
        if($reviewed){
            $course->reviewed_by=$current_user->id;
        }else{
            $course->reviewed_by='';
        }
        
        $course->save();
        
       
         
        return $course;
    }
    
    public function importCourses($file,$isUpdate,$current_user)
    {
        $err_msg='';
        
        $excel=Excel::load($file, function($reader) {             
            $reader->limitColumns(16);
            $reader->limitRows(100);
        })->get();
        

        $courseList=$excel->toArray()[0];
        for($i = 1; $i < count($courseList); ++$i) {
            $row=$courseList[$i];

            if($isUpdate){
                $number=trim($row['number']);
                if(!$number)continue;

                $course=$this->findByNumber($number);
                if(!$course) {
                    $err_msg .= '課程代碼' .$number . '錯誤'. ',';
                    continue;
                }

                $credit_count=(int)trim($row['credit']);
                $credit_price=trim($row['credit_price']);
                $must=(int)trim($row['must']);                

                $tuition=trim($row['tuition']);
                $materials=trim($row['materials']);
                $cost=trim($row['cost']);
                $limit=trim($row['limit']);
                $min=trim($row['min']);

                $description=trim($row['description']);

                $courseValues=[
                    'credit_count' => $credit_count,
                    'credit_price' => $credit_price,
                    'must' => $must,
                    'tuition' => $tuition,
                    'materials' => $materials,
                    'cost' => $cost,
                    'limit' => $limit,
                    'min' => $min,

                    'description' => $description
                ];


                $course->update($courseValues);



            }else{
                $name=trim($row['name']);
                if(!$name)continue;

                $center_code=trim($row['center']);
                $center=$this->centers->getByCode($center_code);
                if(!$center) {
                    $err_msg .=$name . ': '. '中心代碼' .$center_code . '錯誤'. ',';
                    continue;
                }

                $term_nember=trim($row['term']);  
                $term=$this->terms->getByNumber($term_nember);
                if(!$term) {
                    $err_msg .=$name . ': '. '學期別' .$term_nember . '錯誤'. ',';
                    continue;
                }


                $begin_date=trim($row['begin_date']);
                $end_date=trim($row['end_date']);
                $weeks=trim($row['weeks']);
                $hours=trim($row['hours']);

                $courseValues=[
                    'group' => false,
                    'parent' => 0,
                    'term_id' => $term->id,
                    'center_id' => $center->id,
                    'name' => $name,
                    'begin_date' => $begin_date,
                    'end_date' => $end_date,
                    'weeks' => $weeks,
                    'hours' => $hours
                ];

               
                $category_codes= explode(',', trim($row['categories']));
                $categoryIds=Category::whereIn('code',$category_codes)->pluck('id')->toArray();

                $teacher_SIDs=explode(',', trim($row['teachers']));
                $teacherIds=Profile::whereIn('SID',$teacher_SIDs)->pluck('user_id')->toArray();

                $course=$this->store($courseValues , $categoryIds, $teacherIds);
            }
            

            

            

            
        }

        return $err_msg;
    }

    private function syncCategories($categoryIds , $course)
    {
        $course->categories()->sync($categoryIds);
    }
    private function syncTeachers($teacherIds , $course)
    {
        $course->teachers()->sync($teacherIds);
    }
    

     public static function generateNumber($termid, Term $term=null)
     {
        if(!$term)  $term=Term::findOrFail($termid);
       
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
    public function optionsConverting($courseList,$with_empty=false)
    {
        $options=[];
        if($with_empty){
            $item=[ 'text' => '-------' , 
                    'value' => 0 , 
                ];
             array_push($options,  $item);
        }
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