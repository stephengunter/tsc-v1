<?php
namespace App\Services\Course;

use App\Status;
use App\Category;
use App\User;
use App\Profile;

use App\Repositories\CreditCourses;


use DB;
use Exception;
use Excel;


use Carbon\Carbon;

class CreditCourseService
{
   public function __construct(CreditCourses $creditCourses)
   {
      $this->creditCourses=$creditCourses;
      $this->config=config('app.credit_course');
      
   }
   public function typeOptions()
   {
      return $this->config['types'];
   }

   public function findByNumber($number)
   {
      return  $this->creditCourses->findByNumber($number);

   }
   public function findById($id)
   {
      return  $this->creditCourses->findById($id);

   }

   public function importCreditCourses(int $type,$file,$current_user)
   {

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
      $end_date=FormatChecker::getCarbonDate(trim($row['end_date']));
      
      $number=trim($row['number']);
      
      $parent=0;
      if(array_key_exists('parent', $row)){
         $parent_course_number= trim($row['parent']);
         
         if($parent_course_number){
            $parentCourse=$this->findByNumber($parent_course_number);
            if(!$parentCourse){
               $errMsg=$name . ': '. '父課程代碼 ' .$parent_course_number . ' 不存在'; 
              
               throw new RequestError('parent' , $errMsg);
            }else{
               $parent=$parentCourse->id;
            }
         }  
      }

      
      $teacher_SIDs=explode(',', trim($row['teachers']));
      $teacherIds=Profile::whereIn('SID',$teacher_SIDs)->pluck('user_id')->toArray();



      return  [
         'name' => $name,
         'number' => $number,
         'parent' => $parent,
         
         'teacherIds' => $teacherIds
         
      ];
      

   }
}
