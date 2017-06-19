<?php

namespace App\Repositories;

use App\Lesson;
use App\Role;
use App\LessonParticipant;
class LessonParticipants 
{
     public function addTeacher($lesson_id, $teacher_id,$updated_by)
     {
        $lesson=Lesson::findOrFail($lesson_id);
        if($lesson->hasTeacher($teacher_id)){
             return $this->getByUser($lesson_id,$teacher_id);
        }

        $values=[
                    'lesson_id' => $lesson_id,
                    'user_id' => $teacher_id,
                    'status' => 0,  
                    'updated_by' => $updated_by           
                ];

         $teacher=new LessonParticipant($values);
        
         $teacher->role= Role::teacherRoleName();
         $teacher->save();
         return $teacher;
     }
     
     public function addVolunteer($lesson_id, $volunteer_id,$updated_by)
     {
        $lesson=Lesson::findOrFail($lesson_id);
        if($lesson->hasVolunteer($volunteer_id)){
             return $this->getByUser($lesson_id,$volunteer_id);
        }

        $values=[
                    'lesson_id' => $lesson_id,
                    'user_id' => $volunteer_id,
                    'status' => 0,  
                    'updated_by' => $updated_by           
                ];

        $volunteer=new LessonParticipant($values);
        
        $volunteer->role= Role::volunteerRoleName();
        $volunteer->save();
        return $volunteer;
     }

     public function remove($id)
     {
         
         LessonParticipant::destroy($id);
     }

     public function syncTeachers($lesson, $teacherIds)
     {
         $oldTeacherIds=$lesson->teacherIds();
         $lesson_id=$lesson->id;
       
         if(empty($teacherIds)){

             $this->removeTeachers($lesson_id,$oldTeacherIds);

         }else{
            if(empty($oldTeacherIds)){

                 $this->addTeachers($lesson_id , $teacherIds);

            }else{

                $this->addNewTeachers($lesson_id ,$oldTeacherIds, $teacherIds);

                $this->removeOldTeachers($lesson_id,$oldTeacherIds,$teacherIds);
            }
         }
        
     }

     private function addTeachers($lesson_id , $teacherIds)
     {
          for($i = 0; $i < count($teacherIds); $i++) {
                $id=$teacherIds[$i];
                $values=[
                    'lesson_id' => $lesson_id,
                    'user_id' => $id,
                    'status' => 0,              
                    ];

                $this->addTeacher($values);
                
            }
     }
     private function addNewTeachers($lesson_id ,$oldTeacherIds, $teacherIds)
     {
          for($i = 0; $i < count($teacherIds); $i++) {
                 $id=$teacherIds[$i];
                 if (!in_array($id, $oldTeacherIds))
                 {
                     $values=[
                        'lesson_id' => $lesson_id,
                        'user_id' => $id,
                        'status' => 0,              
                     ];

                    $this->addTeacher($values);
                 }
                
           }
     }
     private function removeOldTeachers($lessonId, $oldTeacherIds,$teacherIds)
     {
         for($i = 0; $i < count($oldTeacherIds); $i++) {
            $id=$oldTeacherIds[$i];
            if (!in_array($id, $teacherIds))
            {
                $this->removeByUser($lessonId ,$id);
            }
         }
         
     }
     private function removeTeachers($lessonId,$oldTeacherIds)
     {
          if(!count($oldTeacherIds)) return;

          for($i = 0; $i < count($oldTeacherIds); $i++) {
            $id=$oldTeacherIds[$i];
            $this->removeByUser($lessonId ,$id);
          }
     }

     public function removeByUser($lesson,$user)
     {
          return LessonParticipant::where('lesson_id',$lesson)
                        ->where('user_id',$user)->delete();
     }

     public function getByUser($lesson,$user)
     {
          return LessonParticipant::where('lesson_id',$lesson)
                        ->where('user_id',$user)->first();
     }


     public function syncVolunteers($lesson, $volunteerIds)
     {
         $oldVolunteerIds=$lesson->volunteerIds();
         $lesson_id=$lesson->id;
       
         if(empty($volunteerIds)){

             $this->removeVolunteers($lesson_id,$oldVolunteerIds);

         }else{
            if(empty($oldVolunteerIds)){

                 $this->addVolunteers($lesson_id , $volunteerIds);

            }else{

                $this->addNewVolunteers($lesson_id ,$oldVolunteerIds, $volunteerIds);

                $this->removeOldVolunteers($lesson_id,$oldVolunteerIds,$volunteerIds);
            }
         }
        
     }

     private function addVolunteers($lesson_id , $volunteerIds)
     {
          for($i = 0; $i < count($volunteerIds); $i++) {
                $id=$volunteerIds[$i];
                $values=[
                    'lesson_id' => $lesson_id,
                    'user_id' => $id,
                    'status' => 0,              
                    ];

                $this->addVolunteer($values);
                
            }
     }
     private function addNewVolunteers($lesson_id ,$oldVolunteerIds, $volunteerIds)
     {
          for($i = 0; $i < count($volunteerIds); $i++) {
                 $id=$volunteerIds[$i];
                 if (!in_array($id, $oldVolunteerIds))
                 {
                     $values=[
                        'lesson_id' => $lesson_id,
                        'user_id' => $id,
                        'status' => 0,              
                     ];

                    $this->addVolunteer($values);
                 }
                
           }
     }
     private function removeOldVolunteers($lessonId, $oldVolunteerIds,$volunteerIds)
     {
         for($i = 0; $i < count($oldVolunteerIds); $i++) {
            $id=$oldVolunteerIds[$i];
            if (!in_array($id, $volunteerIds))
            {
                $this->removeByUser($lessonId ,$id);
            }
         }
         
     }
     private function removeVolunteers($lessonId,$oldVolunteerIds)
     {
          if(!count($oldVolunteerIds)) return;

          for($i = 0; $i < count($oldVolunteerIds); $i++) {
            $id=$oldVolunteerIds[$i];
            $this->removeByUser($lessonId ,$id);
          }
     }

   
   
   

  
  
   
   
    
}