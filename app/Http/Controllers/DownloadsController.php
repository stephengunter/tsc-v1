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
        $path='import/';
        $file_name='';
        if($key=='teachers'){
            $file_name = 'teachers.xlsx';
        }else if($key=='teacher-groups'){
            $file_name = 'teacher-groups.xlsx';
        }
        else if($key=='admins'){
            $file_name = 'admins.xlsx';
        }else if($key=='centers'){
            $file_name = 'centers.xlsx';
        }
        else if($key=='categories'){
            $file_name = 'categories.xlsx';
        }
        else if($key=='courses'){
            $file_name = 'courses.xlsx';
        }else if($key=='group-courses'){
            $file_name = 'group-courses.xlsx';
        }else if($key=='course-infoes'){
            $file_name = 'course-infoes.xlsx';
        }

       

        if(!$file_name) abort(404);

        $path .= $file_name;
      
        
        return response()->download(storage_path($path));
    }

     
}
