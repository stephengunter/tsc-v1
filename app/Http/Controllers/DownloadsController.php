<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Input;
use App\Repositories\Downloads;
use App\Download;

use File;

class DownloadsController extends BaseController
{
    
    protected $key='main_settings';
    public function __construct(Downloads $downloads) 
    {
        $this->downloads=$downloads;
		
	}
    public function index()
    {
        $current_user=$this->currentUser();

        $request = request();
        if($request->ajax()){
            $canEdit=Download::canEdit($current_user);
            $downloads=$this->downloads->getAll()
                                ->orderBy('order','desc')->get();
            
            foreach($downloads as $download){
                $download->canDelete=$download->canDeleteBy($current_user);
            }
            
          
            return response()
            ->json([
                'downloads' => $downloads,
                'canEdit' => $canEdit
            ]);
        } 

        
        $type=$request->type;
        
        if($type=='import'){
            $key=$request->key;
            return $this->importTemps($key);
        }

        $menus=$this->menus($this->key);            
        return view('downloads.index')
                ->with(['menus' => $menus]);



    }
    public function create()
    {
        
        $download= Download::initialize();
        return response()->json(['download' => $download]);       
            
    }

    public function store(Request $form)
    {
        $current_user=$this->currentUser();

        $file=null;
        if(isset($_FILES['filedata']))  $file=Input::file('filedata');

        $id=(int)$form['id'];
        $title=$form['title'];

        $download=null;
        if($id){
            $download=$this->downloads->update($id,$current_user ,$title,$file);
           
        }else{
            if($file){
                $download=$this->downloads->store($title,$file,$current_user);
                
            }else{
                abort(500);
            }
        }

        return response()->json($download);

       
    }
    public function show($id)
    {
        $download=Download::findOrFail($id);

        
       
        return response()->download($download->getStoragePath());
    }

    public function edit($id)
    {
        $download=Download::findOrFail($id);    
        $current_user=$this->currentUser();
        if(!$download->canEditBy($current_user)){
            return  $this->unauthorized();       
        }

        return response()->json(['download' => $download ]);        
                  
    }
    public function updateDisplayOrder(Request $form)
    {
        $current_user=$this->currentUser();

        $downloads=$form['downloads'];
        for($i = 0; $i < count($downloads); ++$i) {
            $download=$downloads[$i];

            $id=$download['id'];
            $order=$download['order'];
            $updated_by=$current_user->id;

            $this->downloads->updateDisplayOrder($id,$order,$updated_by);
            
        }


        return response()->json(['success' => true]);

    }
    public function destroy($id)
    {
        $current_user=$this->currentUser();
        $download=Download::findOrFail($id);
        if(!$download->canDeleteBy($current_user)){
            return  $this->unauthorized();   
        }
        $this->downloads->delete($id,$current_user->id);

        return response()
            ->json([
                'deleted' => true
            ]);
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
      
          dd(storage_path($path));
        return response()->download(storage_path($path));
    }

     
}
