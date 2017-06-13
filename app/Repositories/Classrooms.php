<?php

namespace App\Repositories;

use App\Classroom;

class Classrooms 
{
    public function getAll()
    {
        return Classroom::where('removed',false);
    }
     
    public function getByCenter($center , $withCenter)
    {
        $allClassrooms=$this->getAll();
        if(!$withCenter){
            return  $allClassrooms->where('center_id',$center);
        }

        return $allClassrooms->with('center')->where('center_id',$center);
       
    }
    public function store($values)
    {
        return Classroom::create($values);
    }
    public function update($values, $id)
    {
         $classroom=$this->findOrFail($id);     

         $classroom->update($values);

          return $classroom;
    }

    public function findOrFail($id)
    {
        return Classroom::findOrFail($id);     
    }
   
    public function options($center)
    {
        $classroomOptions=[];
        $classroomList=$this->getAll()->where('active',true)->get();
       
        foreach($classroomList as $classroom)
        {
            $item=[ 'text' => $classroom->name , 
                     'value' => $classroom->id , 
                 ];
            array_push($classroomOptions,  $item);
        }

          return $classroomOptions;
       
    }
    public function delete($id)
    {
        $classroom = Classroom::findOrFail($id);

        $classroom->active=0;
        $classroom->removed=1;
        $classroom->save();
        
    }
  
   
   
    
}