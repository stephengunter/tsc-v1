<?php

namespace App\Repositories;

use App\Download;


class Downloads 
{
    public function getAll()
    {
         return Download::where('removed',false);
    }

    public function updateDisplayOrder($id,$order,$updated_by)
    {
        $download = Download::findOrFail($id);        

        $download->order= (int)$order;           
        $download->updated_by= $updated_by;

        if($order>=0){
            $download->active=true;
        }else{
            $download->active=false;
        }
        
        
        $download->save();
       
        return $download;

    }

    public function delete($id,$admin_id)
    {
        $download = Download::findOrFail($id);

        $values=[
            'active' =>0,
            'removed' => 1,
            'updated_by' => $admin_id
        ];
        
        $download->update($values);
        
    }
}