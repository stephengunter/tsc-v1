<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Http\Requests\PhotoRequest;

use Carbon\Carbon;
use Illuminate\Support\Facades\Input;

use App\Repositories\Files;

class FilesController extends BaseController
{
    
   
    public function __construct(Files $files)                                
    {
                  
          
          $this->files=$files;
          
    }
    public function upload(Request $form)
    {
        if(!$form->hasFile('upload_file')){
            return   response()
                        ->json(['upload_file' => ['無法取得上傳檔案'] 
                            ]  ,  422);      
        }

        $file=Input::file('upload_file');

        $file=$this->files->save_temp_file($file);
       
        return response()->json($file);
    }
}
