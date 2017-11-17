<?php

namespace App\Repositories;

use App\Download;
use Storage;
use File;

use Illuminate\Auth\AuthenticationException;


class Downloads 
{
    public function __construct()                                          
    {
        $this->disk=Storage::disk('public');
        
    }

    private function createFileName($file)
    {
        return \App\File::createFileName($file);
      
    }

    public function saveFile($file)
    {
        $random_file_name=$this->createFileName($file);
        $save_path= config('app.downloads.folder') . $random_file_name;
        $this->disk->put($save_path ,  File::get($file));

        return $random_file_name;
    }
    
    public function getAll()
    {
         return Download::where('removed',false);
    }
    public function activeItems($order=false)
    {
         $downloads = $this->getAll()->where('active',true);

         if(!$order) return $downloads;

         return $downloads->orderBy('order','desc');
         
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

    public function store($title,$file,$current_user)
    {
        if(!Download::canEdit($current_user)){
            throw new AuthenticationException();          
        }
        $path=$this->saveFile($file);

        return Download::create([
            'title' => $title,
            'name' => $file->getClientOriginalName(),
            'type' => $file->getClientOriginalExtension(),
            'path' => $path,
            'updated_by' => $current_user->id
        ]);
    }

    public function update($id,$current_user , $title,$file=null)
    {
        $download=Download::findOrFail($id); 
        if(!$download->canEditBy($current_user)){
            throw new AuthenticationException();       
        }
        
        if($file){
            $path=$this->saveFile($file);
            $download->update([
                'title' => $title,
                'name' => $file->getClientOriginalName(),
                'type' => $file->getClientOriginalExtension(),
                'path' => $path,
                'updated_by' => $current_user->id
            ]);
        }else{
            $download->update([
                'title' => $title,
                'updated_by' => $current_user->id
            ]);
        }

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