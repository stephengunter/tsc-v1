<?php
namespace App\Services\Course;

use App\Course;
use App\Status;
use App\Category;
use App\User;
use App\Profile;

use App\Repositories\Courses;
use App\Repositories\Centers;
use App\Repositories\Terms;
use App\Repositories\Weekdays;
use App\Repositories\Teachers;
use App\Repositories\Categories;

use App\Exceptions\RequestError;
use App\Support\Helper;
use App\Support\FormatChecker;

use DB;
use Exception;
use Excel;

use Carbon\Carbon;


class CourseService
{
   public function __construct(Courses $courses, Categories $categories, Teachers $teachers,
                                 Terms $terms , Centers $centers, Weekdays $weekdays)
                               
    {
       
        $this->courses=$courses;
        $this->categories=$categories;
        $this->teachers=$teachers;
        $this->terms=$terms;
        $this->centers=$centers;
        $this->weekdays=$weekdays;

    }
   public function termOptions()
   {
      return  $this->terms->options();
   }
   public function centerOptions($empty_item=false)
   {
      return  $this->centers->options($empty_item);

   }

   public function teacherOptions($center_id)
   {
       $reviewed=true;
       return $this->teachers->optionsByCenter($center_id,$reviewed);
   }

   public function categoryOptions($public=false)
   {
      return  $this->categories->options($public);

   }
   public function weekdayOptions()
   {
      return $this->weekdays->options();

   }

   public function options(array $params,$with_empty=false)
   {
       $courses=$this->index($params,[])
                      ->get();

                    

       
       return $this->courses->optionsConverting($courses,$with_empty);
   }

   public function findByNumber($number)
   {
      return  $this->courses->findByNumber($number);

   }

   public function courseNumberExist($number,$id)
   {
      return $this->courses->numberExist($number,$id);
   }
   
   public function index(array $params ,array $with=[])
   {
      
      return $this->courses->index($params ,$with);
   }

   public function store(array $values, array $category_ids=[], array $teacher_ids=[] )
   {
      $values['active'] =true;
      $values['reviewed'] =false;
      $values['credit'] =false;
      $values['group'] =0;
      $values['parent'] =0;

      $course=new Course($values);
      return $this->insertCourse($course,$category_ids,$teacher_ids);

   }

   

   private function storeGroupParentCourse(array $values, array $category_ids)
   {
      $values['group'] = true;
      $values['parent'] = 0;
      $values['weeks'] = 0;
      $values['hours'] = 0;

      $course=new Course($values);

      return $this->insertCourse($course,$category_ids);

   }
   private function storeGroupSubCourse(int $parent,array $values,array $teacher_ids ,array $category_ids=[])
   {
      $course=new Course($values);
      $parent_course=$course->copyParentCourseValues($parent);

      if(!count($category_ids)){
         $category_ids=$parent_course->categories->pluck('id')->toArray();
      }

      return $this->insertCourse($course,$category_ids,$teacher_ids);
   }

   private function insertCourse(Course $course,array $category_ids=[],array $teacher_ids=[])
   {
      
      $course= DB::transaction(function() use($course,$category_ids,$teacher_ids) {

         $course->save();
         
         $statusValues=Status::initialize($course);
         
         $status=new Status($statusValues);
         
         $course->status()->save($status);

         if(count($category_ids)){
            $course->categories()->sync($category_ids);
         }

         if(count($teacher_ids)){
            $course->teachers()->sync($teacher_ids);
         }

         return $course;
            
      });

      return $course;
   }

   public function importCourses($file,$current_user)
   {
      $err_msg='';
      $excel=$this->getExcelObject($file);  

      $courseList=$excel->toArray()[0];
      
      for($i = 1; $i < count($courseList); ++$i) {
         
         $row=$courseList[$i];
         
         try {
            $values=$this->getCourseValuesFromRow($row);
         }
         catch (RequestError $err) {
            if($err->getErrorMsg()){
               $err_msg .= $err->getErrorMsg() . ',';
            }
            
            continue;
         }
         

         $name=$values['name'];
         
         $number=$values['number'];
         if($number){
            $numberExist=$this->courseNumberExist($number,0);
            
            if($numberExist){
                  $err_msg .=$name . ': '. '課程代碼 ' .$number . ' 重複了'. ',';
                  continue;
            }
         }

         $categoryIds=$values['categoryIds'];
         $teacherIds=$values['teacherIds'];

         $values['updated_by'] =$current_user->id;
         $course=$this->store($values , $categoryIds, $teacherIds);
      
         
      }  //End For

      return $err_msg;
   } 

   private function getExcelObject($file)
   {
      $excel=Excel::load($file, function($reader) {             
         $reader->limitColumns(16);
         $reader->limitRows(100);
      })->get();

      return $excel;
   }


   private function getCourseValuesFromRow($row)
   {
      $name=trim($row['name']);
      if(!$name) {
        
         throw new RequestError('name','');
      }

      
      $begin_date=FormatChecker::getCarbonDate(trim($row['begin_date']));

      if(!$begin_date) {
         $errMsg=$name . ': ' . '開始日期錯誤';
         throw new RequestError('begin_date',$errMsg);
      }

      $end_date=FormatChecker::getCarbonDate(trim($row['end_date']));
      
      if(!$end_date) {
            $errMsg=$name . ': ' . '結束日期錯誤';
            throw new RequestError('end_date',$errMsg);
      }



      $level=trim($row['level']);
      $number=trim($row['number']);
      $description=trim($row['description']);

      $center_code=trim($row['center']);
      $center=$this->centers->getByCode($center_code);
      if(!$center) {
         
         $errMsg=$name . ': ' . '中心代碼' . $center_code . '錯誤';
         throw new RequestError('center', $errMsg);
      }

      $term_nember=trim($row['term']);  
      $term=$this->terms->getByNumber($term_nember);
      if(!$term) {
         $errMsg=$name . ': '. '學期別' .$term_nember . '錯誤';
         
         throw new RequestError('term' , $errMsg);
      }

      
      // $parent=0;
      // if(array_key_exists('parent', $row)){
      //    $parent_course_number= trim($row['parent']);
         
      //    if($parent_course_number){
      //       $parentCourse=$this->findByNumber($parent_course_number);
      //       if(!$parentCourse){
      //          $errMsg=$name . ': '. '父課程代碼 ' .$parent_course_number . ' 不存在'; 
              
      //          throw new RequestError('parent' , $errMsg);
      //       }else{
      //          $parent=$parentCourse->id;
      //       }
      //    }  
      // }
     
      $weeks=trim($row['weeks']);
      $hours=trim($row['hours']);

      $category_codes= explode(',', trim($row['categories']));
      $categoryIds=Category::whereIn('code',$category_codes)->pluck('id')->toArray();

      $teacher_SIDs=explode(',', trim($row['teachers']));
      $teacherIds=Profile::whereIn('SID',$teacher_SIDs)->pluck('user_id')->toArray();



      return  [
         'name' => $name,
         'number' => $number,
         'level' => $level,
         'description' => $description,
       
      //    'parent' => $parent,
         'term_id' => $term->id,
         'center_id' => $center->id,
         
         'begin_date' => $begin_date,
         'end_date' => $end_date,
         'weeks' => $weeks,
         'hours' => $hours,

         'categoryIds' => $categoryIds,
         'teacherIds' => $teacherIds
         
      ];
      

   }

//    public function importGroupCourses($file,$current_user)
//    {
//       $err_msg='';
//       $excel=$this->getExcelObject($file);      

//       $courseList=$excel->toArray()[0];
      
//       if(!array_key_exists('parent',$courseList[1])){
//          return '檔案格式不正確';
//       }
      
//       for($i = 1; $i < count($courseList); ++$i) {
//          $row=$courseList[$i];

//          try {
//             $values=$this->getCourseValuesFromRow($row);
//          }
//          catch (RequestError $err) {
//             if($err->getErrorMsg()){
//                $err_msg .= $err->getErrorMsg() . ',';
//             }
            
//             continue;
//          }

//          $values['group'] =1;
//          $values['updated_by'] =$current_user->id;

         

//          $name=$values['name'];

//          $number=$values['number'];
//          if($number){
//             $numberExist=$this->courseNumberExist($number,0);

            
//             if($numberExist){
//                   $err_msg .=$name . ': '. '課程代碼 ' .$number . ' 重複了'. ',';
//                   continue;
//             }
//          }

         

//          $categoryIds=$values['categoryIds'];
//          $teacherIds=$values['teacherIds'];

//          $course=$this->store($values , $categoryIds, $teacherIds);

//       } //End For

//       return $err_msg;
//    }

   public function importCourseInfoes($file,$current_user)
   {
      $err_msg='';
      $excel=$this->getExcelObject($file);     
       

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

         $caution=trim($row['caution']);
         if($caution){
            $caution= str_replace(';', '<br>',$caution);
         }

         $materials=trim($row['materials']);
         if($materials){
            $materials= str_replace(';', '<br>',$materials);
         }

         $target=trim($row['target']);

         $limit=trim($row['limit']);
         $min=trim($row['min']);

         $tuition=(float)trim($row['tuition']);
         $cost=(float)trim($row['cost']);

         $courseValues=[
            'caution' => $caution,
          
            'tuition' => $tuition,
            'materials' => $materials,
            'cost' => $cost,
            'target' => $target,
            'limit' => $limit,
            'min' => $min,
           
           
            'updated_by' => $current_user->id
         ];


         $course->update($courseValues);


      //    if($course->groupAndParent())
      //    {
      //       $courseValues=[
      //          'caution' => $caution,
              
      //          'materials' => $materials,
               
      //          'target' => $target,
      //          'limit' => $limit,
      //          'min' => $min,
              
      //          'updated_by' => $current_user->id
      //       ];

      //       $course->update($courseValues);

      //       continue;
      //    }

      //    $credit_count=(int)trim($row['credit']);
      //    $credit_price=(float)trim($row['credit_price']);
      //    $must=(int)trim($row['must']);                

         
         
         
      }  //End For

      return $err_msg;
   }

}