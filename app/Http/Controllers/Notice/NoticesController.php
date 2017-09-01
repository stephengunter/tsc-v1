<?php

namespace App\Http\Controllers\Notice;

use App\Http\Controllers\BaseController;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\Http\Requests\Notice\NoticeRequest;

use App\Notice;
use App\File;
use App\Course;
use App\Email;
use App\Repositories\Notices;
use App\Support\Helper;
use App\Http\Middleware\CheckAdmin;

use App\Events\NoticeMailCreated;
use Carbon\Carbon;
use Storage;


class NoticesController extends BaseController
{
    protected $key='notices';
    public function __construct(Notices $notices,CheckAdmin $checkAdmin)                                          
    {
         $exceptAdmin=[];
         $allowVisitors=[];
		 $this->setMiddleware( $exceptAdmin, $allowVisitors);
		
         $this->notices=$notices;

         $this->setCheckAdmin($checkAdmin);

	}
    public function index()
    {
        if(!request()->ajax()){
            $menus=$this->menus($this->key);            
            return view('notices.index')
                    ->with(['menus' => $menus]);
        }   
        $noticeList=$this->notices->getAll()->filterPaginateOrder();

        return response() ->json(['model' => $noticeList  ]);  
       
    }
    public function create()
    {
        $request = request();
        if(!$request->ajax()){
            $menus=$this->menus($this->key);            
            return view('notices.create')
                   ->with([ 'menus' => $menus  ]);           
                       
        }  

        $notice=Notice::initialize();

        return response()
            ->json([
                'notice' => $notice
            ]);
         
    }
    
    public function store(NoticeRequest $request)
    {
        $current_user=$this->currentUser();
        $updated_by=$current_user->id;
        $removed=false;
        $values=$request->getValues($updated_by,$removed);
      
        $attachments=$request->getFiles();
       
        $attachment_ids='';
        if($attachments && count($attachments)){
            $disk=Storage::disk('upload_files');
            foreach ($attachments as $attachment) {
                $file_path=$attachment['path'];
                $entity=new \App\File([
                    'title' => $attachment['title'],
                    'path' => $file_path,
                    'mime' => $attachment['mime'],
                ]);
                $entity->save();
               
                $attachment_ids .= $entity->id .',';
                $temp_path='temp/' . $file_path;
                $disk->move($temp_path, $file_path);
           }
        }
        $attachment_ids=Helper::removeLastComma($attachment_ids);
       
        $values['attachments'] = $attachment_ids;

       

        $emails=(int)$values['emails'];

        $public=(bool)$values['public'];
        if($public){
            if(!$values['date']){
                $values['date']=Carbon::now()->toDateString();
            }
        }
      
        if($emails && $values['courses']){
            $courseIds= explode(',', $values['courses']);
            $notice=$this->notices->store($values , $courseIds);
            $courses=$notice->courses;
            $receivcers='';
        
            $email=new Email([
                'notice_id' => $notice->id,
                'title'=> $notice->title,
                'content'=> $notice->content,
                'updated_by' =>  $notice->updated_by,
            ]);
            foreach ($courses as $course) {
                $students=$course->students;
                foreach ($students as $student) {
                     $user=$student->user;
                     $receivcers .= $user->id . ',';
                     dispatch(new \App\Jobs\SendEmail($notice, $user));   
                     
                    
                }
    
            }

            $receivcers=Helper::removeLastComma($receivcers);
            $email->receivers=$receivcers;
            $email->attachments=$attachment_ids;
            $email->save();
            
            //event(new NoticeMailCreated($notice));
            

            return response() ->json($notice);
        }else{
           
            $notice= Notice::create($values);
            return response() ->json($notice);
        }
        
    }
    public function show($id)
    {
        if(!request()->ajax()){
            $menus=$this->menus($this->key);            
            return view('notices.details')
                    ->with([ 'menus' => $menus,
                              'id' => $id     
                        ]);
        }  
        $current_user=$this->currentUser();
        $notice = Notice::findOrFail($id);
        
        $notice->canEdit=$notice->canEditBy($current_user);
        $notice->canDelete=$notice->canDeleteBy($current_user);

        if(count($notice->courses)){
            $notice->courseNames= $notice->courseNames();
        } 

        return response()->json(['notice' => $notice]);
    }
    public function edit($id)
    {
        $current_user=$this->currentUser();
        $notice = Notice::findOrFail($id);
        if(!$notice->canEditBy($current_user)){
            return  $this->unauthorized(); 
        }
       
        return response()
            ->json([
                'notice' => $notice
            ]);
    }
    public function update(NoticeRequest $request, $id)
    {
        $current_user=$this->currentUser();
        $notice = Notice::findOrFail($id);
        if(!$notice->canEditBy($current_user)){
            return  $this->unauthorized();         
        }
        $removed=false;
        $updated_by=$current_user->id;

        $values=$request->getValues($updated_by,$removed); 
       
        $notice->update($values);
        
        return response()->json(['notice' => $notice ]);
               
    }
    public function destroy($id)
    {
        $current_user=$this->currentUser();
        $notice = Notice::findOrFail($id);
        if(!$notice->canDeleteBy($current_user)){
           return  $this->unauthorized();     
        }
        $this->notices->delete($id,$current_user->id);

        return response()->json([ 'deleted' => true ]);
           
    }
}
