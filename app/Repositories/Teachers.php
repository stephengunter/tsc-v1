<?php

namespace App\Repositories;

use App\Teacher;
use App\Course;
use App\Center;
use App\Role;

use App\Support\Helper;


class Teachers 
{
    public function getRoleName()
    {
        return Role::teacherRoleName();
    }
    public function initialize()
    {
        return Teacher::initialize();
    }
    public function getAll()
    {
         return Teacher::where('removed',false);
    }
    public function getByCenter($center_id)
    {
        $teachers=$this->getAll()->whereHas('centers', function($q) use ($center_id)
        {
            $q->where('id',$center_id );
        });
        return $teachers;
    }
    public function findOrFail($id)
    {
        $teacher = Teacher::findOrFail($id);
        return $teacher;
       
    }
    public function groupTeachers($id)
    {
        $teacher = $this->findOrFail($id);
        $teacher_ids=$teacher->teacher_ids;
        $ids= explode( ',', $teacher_ids );
        return $this->getAll()->whereIn('user_id',$ids)
                              ->with('user.profile');
        if($teacher_ids){
           $ids= explode( ',', $teacher_ids );
           return $this->getAll()->whereIn('user_id',$ids)
                                 ->with('user.profile');
        }

        return null;
       
    }
    
    public function store($user ,$values)
    {
        $teacher=$user->teacher;
        if(!$teacher){
            $teacher=new Teacher($values);  
            $user->teacher()->save($teacher);
        }else{
            $user->teacher->update($values);
        }
        return  $user;
    }

    public function update($values,$id)
    {
         $teacher = Teacher::findOrFail($id);
         $teacher->update($values);

         return $teacher;
    }

    

    public function delete($id,$admin_id)
    {
         $teacher = Teacher::findOrFail($id);
         $values=[
            'active' =>0,
            'reviewed' =>0,
            'removed' => 1,
            'updated_by' => $admin_id
         ];
        
         $teacher->update($values);
    }
   

    public function optionsByCenter($center_id)
    {
        $teachers=$this->getByCenter($center_id)->where('active',true)->get();
        
        return $this->optionsConverting($teachers);
       
    }

    public function options($course)
    {
        $course=Course::findOrFail($course);
        return $this->optionsConverting($course->teachers);
       
    }


    public function optionsConverting($teacherList)
    {
        $options=[];
        foreach($teacherList as $teacher)
        {
            $item=[ 'text' => $teacher->getName() , 
                     'value' => $teacher->user_id , 
                 ];
            array_push($options,  $item);
        }

        return $options;
    }
    
    public function attachCenter($teacher_id,$center_id)
    {
            $teacher=$this->findOrFail($teacher_id); 
            $teacher->attachCenter($center_id);
            
            return $teacher;
    }
    public function detachCenter($teacher_id,$center_id)
    {
            $teacher=$this->findOrFail($teacher_id); 
            $teacher->detachCenter($center_id);
            return $teacher;
    }
    
    
}