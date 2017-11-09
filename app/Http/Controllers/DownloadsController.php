<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

class DownloadsController extends BaseController
{
    

    public function index()
    {
        $request = request();
        $type=$request->type;
        $key=$request->key;
        if($type=='import'){
            return $this->importTemps($key);
        }
    }


    private function importTemps($key)
    {
        $files=[
            'teachers' => 'teachers.xlsx',
            'teacher-groups' => 'teacher-groups.xlsx',

            'admins' => 'admins.xlsx',
            'centers' => 'centers.xlsx',
            'categories' => 'categories.xlsx',

            'courses' => 'courses.xlsx',
            'group-courses' => 'group-courses.xlsx',
            'course-infoes' => 'course-infoes.xlsx',

            'schedules' => 'schedules.xlsx',
        ];

        $path='import/';
        $file_name='';
        

        if(array_key_exists($key,$files)){
            $file_name=$files[$key];
        } 

        if(!$file_name) abort(404);

        $path .= $file_name;
      
        
        return response()->download(storage_path($path));
    }

     
}
