<?php

namespace App\Repositories;

use App\Course;
use App\Status;
use App\Category;
use App\Center;
use App\Teacher;
use App\Profile;
use App\Schedule;


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
         return Course::where('removed',false)->where('credit',false);
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
    public function numberExist($number,$id)
    {
       
        $number_exist= $this->getAll()->where('number',$number)
                                      ->where('id','!=',$id)->first();
        if($number_exist) return true;
        return false;
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
    public function getActiveCourses(int $categoryId=0,int $centerId=0,array $with=[])
    {
        if(!count($with)) $with=['center','categories','teachers','classTimes'];
        $courseList=$this->getAll()->with($with)->where('active',true)->where('reviewed',true);

        $activeTermIds=Term::where('active',true)->pluck('id')->toArray();
        $courseList=$courseList->whereIn('term_id',$activeTermIds);
        
        


        if($categoryId){
             $courseList= $courseList->whereHas('categories', function($q)  use ($categoryId)
             {
                $q->where('id', $categoryId );
             });
        }

        

        if($centerId) $courseList=$courseList->where('center_id',$centerId);

        return $courseList;
        
    }
    public function index(array $params ,array $with=[])
    {
        $termId=Helper::getIntegerByKey($params, 'term');
        $centerId=Helper::getIntegerByKey($params, 'center');
        $categoryId=Helper::getIntegerByKey($params, 'category');
        $teacherId=Helper::getIntegerByKey($params, 'teacher');
        $weekdayId=Helper::getIntegerByKey($params, 'weekday');
        $parentId=Helper::getIntegerByKey($params, 'parent');

        $courseList=null;

        if(count($with)){
            $courseList=$this->getAll()->with($with);
        }else{
            
            $courseList=$this->getAll();
        } 
        
        
        if($termId) $courseList->where('term_id',$termId);       

        if($centerId) $courseList->where('center_id',$centerId);

        if($parentId > 0){
            $courseList->where('parent',$parentId);
        }else if($parentId <0 ){
            $courseList->where('parent', 0);
        } 

        $hasReviewed =array_key_exists('reviewed', $params);
        if($hasReviewed){
            $reviewed=(bool)$params['reviewed'];
            $courseList=$courseList->where('reviewed',$reviewed);
        };       


        

        if($categoryId){
            $courseList= $courseList->whereHas('categories', function($q)  use ($categoryId)
            {
                $q->where('id', $categoryId );
            });
        }

        if($teacherId){
            $courseList= $courseList->whereHas('teachers', function($q) use ($teacherId)
            {
                $q->where('user_id', $teacherId);
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
        dd('removed');

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
        use($course,$courseValues){
              
            $course->save();

            if(array_key_exists('ps',$courseValues)){
                $course->ps=$courseValues['ps'];
               
            }
              
              
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
    public function update(Course $course ,$courseValues , $categoryIds, $teacherIds)
    {
        $course->update($courseValues);

        $this->syncCategories($categoryIds , $course);    
        $this->syncTeachers($teacherIds , $course);

        $course->status->updateStatus();

        return $course;
    }
    public function updateNumber($id,$custom_number,$updated_by)
    {
        $course=$this->findOrFail($id);          
         
        $default_number=$course->generateNumber();

        $number=$default_number . $custom_number; 

        $number_exist=$this->numberExist($number,$id);
        if($number_exist) return null;
         
        $course->number= $number;           
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
    
   
    
    public function importCourseInfoes($file,$current_user)
    {
        $err_msg='';
        
        $excel=Excel::load($file, function($reader) {             
            $reader->limitColumns(16);
            $reader->limitRows(100);
        })->get();
        

        $courseList=$excel->toArray()[0];
        for($i = 1; $i < count($courseList); ++$i) {
            $row=$courseList[$i];
            
            $number=trim($row['number']);
            if(!$number)continue;

            $course=$this->findByNumber($number);
            if(!$course) {
                $err_msg .= '找不到代碼 = ' .$number . '的課程'. ',';
                continue;
            }

            $credit_count=(int)trim($row['credit']);
            $credit_price=(float)trim($row['credit_price']);
            $must=(int)trim($row['must']);                

            $tuition=(float)trim($row['tuition']);
            $materials=trim($row['materials']);
            if($materials){
               $materials= str_replace(';', '<br>',$materials);
            }

            
            $cost=(float)trim($row['cost']);


            $limit=trim($row['limit']);
            $min=trim($row['min']);
            $target=trim($row['target']);

            $net_signup=(int)trim($row['net_signup']);

            $description=trim($row['description']);
            $caution=trim($row['caution']);
            if($caution){
                $caution= str_replace(';', '<br>',$caution);
            }

            $courseValues=[
                'caution' => $caution,
                'credit_count' => $credit_count,
                'credit_price' => $credit_price,
                'must' => $must,
                'tuition' => $tuition,
                'materials' => $materials,
                'cost' => $cost,
                'target' => $target,
                'limit' => $limit,
                'min' => $min,
                'net_signup' => $net_signup,
                'description' => $description,
                'updated_by' => $current_user->id
            ];


            $course->update($courseValues);
           
            
        }  //End For

        return $err_msg;
    }

    public function copyCourse(Course $old_course,$term_id, $center_id , $updated_by , $parent_id=0)
    {
        $courseValues=$old_course->toArray();
        
        $courseValues['term_id']=$term_id;
        $courseValues['center_id']=$center_id;
        
        $courseValues['number']='';
        $courseValues['begin_date']='';
        $courseValues['end_date']='';
        $courseValues['open_date']='';
        $courseValues['close_date']='';

        $courseValues['reviewed']=false;
        $courseValues['active']=true;
        $courseValues['removed']=false;
        $courseValues['updated_by']=$updated_by;
        $courseValues['parent']=$parent_id;

        $categoryIds = $old_course->privateCategories()
                                    ->pluck('id')->toArray();
        
        $teacherIds = $old_course->teachers()->get()
                                    ->pluck('user_id')->toArray(); 

        $course = $this->store($courseValues , $categoryIds, $teacherIds);

        if(count($old_course->schedules)){
            foreach ($old_course->schedules as $schedule) {
                $scheduleValues=$schedule->toArray();
                $scheduleValues['course_id']=$course->id;
                Schedule::create($scheduleValues);
            }

        }


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
            $item=[ 'text' => $course->fullName(), 
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