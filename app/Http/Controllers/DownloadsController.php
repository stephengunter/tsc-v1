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
        }else if($key=='admins'){
            $file_name = 'admins.xlsx';
        }else if($key=='centers'){
            $file_name = 'centers.xlsx';
        }
        else if($key=='categories'){
            $file_name = 'categories.xlsx';
        }


        if(!$file_name) abort(404);

        $path .= $file_name;
        
        //$path = storage_path('uploads/xy.zip');
      
       
        
    
       
            // dd($contents);
    
    
        // $file = public_path()."/downloads/info.pdf";
        // $headers = array('Content-Type: application/pdf',);
        // return Response::download($file, 'info.pdf',$headers);
      
        
        return response()->download(storage_path($path));
    }

     
}
