<?php

namespace App\Repositories;

use App\Notice;

class Notices 
{
    public function getAll()
    {
         return Notice::where('removed',false);
    }
    public function findOrFail($id)
    {
         return Notice::findOrFail($id);
    }
  
    public function activeNotices()
    {
        $noticeList=$this->getAll();
        return $noticeList->where('active',true);
    }
    public function store($values , $courseIds)
    {
        $notice= Notice::create($values);
        
        $this->syncCourses($courseIds , $notice);

        return $notice;
    }

    public function delete($id,$admin_id)
    {
        $notice = Notice::findOrFail($id);
       
         $values=[
            'active' =>0,
            'removed' => 1,
            'updated_by' => $admin_id
         ];
        
         $notice->update($values);
        
    }

    private function syncCourses($courseIds , $notice)
    {
        $notice->courses()->sync($courseIds);
    }

    
    
}