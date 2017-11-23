<?php

namespace App\Repositories;

use App\Term;

class Terms 
{
    public function getAll()
    {
        return Term::where('removed',false); 
    }
    public function activeTerms()
    {
        return $this->getAll()->where('active',true);
         
    }
    public function latest()
    {
        return  $this->activeTerms()
                ->orderBy('number','desc')->first();
    }

    public function checkNumber($number,$id)
    {
        $exist= $this->getAll()->where('number',$number)
                               ->where('id','!=',$id)
                                ->first();
       
        if($exist) return false;
        return true;

    }


    public function findOrFail($id)
    {
        return Term::findOrFail($id); 
         
    }

    public function getByNumber($number)
    {
        return $this->getAll()->where('number',$number)->first();
    }
    
    public function store($values)
    {
        $term=Term::create($values);

        return $term;
    }
    public function update($values,$id)
    {
        $term=Term::findOrFail($id); 
        if($term->open_date == $values['open_date'] &&  $term->close_date == $values['close_date'] ){
            $term->update($values);
            
        }else{
            //報名日期有變動
            $term->update($values);
            $this->updateCourses($term);
        }
        
        $term->update($values);

        return $term;
    }

    private function updateCourses(Term $term)
    {
       
        foreach ($term->courses as $course) {
            $course->open_date=$term->open_date;
            $course->save();
           
        }
    }
    
    
    public function delete($id,$admin_id)
    {
        $term = Term::findOrFail($id);

        $values=[
            'active' =>0,
            'removed' => 1,
            'updated_by' => $admin_id
        ];
        
        $term->update($values);
        
    }

    public function options()
    {
        $terms=$this->getAll()->orderBy('number','desc')->get();
        return $this->optionsConverting($terms);
    }
    public function optionsConverting($terms)
    {
        $options=[];
        foreach($terms as $term)
        {
             $item=[ 'text' => $term->name , 
                     'value' => $term->id , 
                 ];
            array_push($options,  $item);
        }
          return $options;
    }
   
    
}