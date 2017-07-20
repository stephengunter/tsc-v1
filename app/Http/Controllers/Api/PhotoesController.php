<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Http\Requests\PhotoRequest;

use Illuminate\Support\Facades\Input;

use App\Repositories\Photoes;
use App\Photo;

class PhotoesController extends BaseController
{
    private  $default_folder = '/images/uploads/';

    public function __construct(Photoes $photoes)                                
    {
        $this->middleware('auth:api');
        $this->photoes=$photoes;
	}
    public function show($id)
    {
        $photo = Photo::findOrFail($id);
        return response()
            ->json([
                'photo' => $photo
            ]);
    }
    public function store(Request $form)
    {
        if(!$form->hasFile('image_file')){
            return   response()
                        ->json(['image_file' => ['無法取得上傳圖檔'] 
                            ]  ,  422);      
        }

        $file=Input::file('image_file');
      
        $width= $form['width'];
        $height= $form['height'];
      
        $folder_name= $this->default_folder;
        
        $save_path = $this->photoes->save_upload_photo($file ,$width , $height, $folder_name);

        $photo=new Photo();
        $photo->fill(Input::all());
       
        $photo->path=$save_path ;

        $photo->save();
       
        return response()->json($photo);

    }

    
}
