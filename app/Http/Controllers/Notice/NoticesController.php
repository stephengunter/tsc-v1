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
use App\Repositories\Files;
use App\Support\Helper;
use App\Http\Middleware\CheckAdmin;

use App\Events\NoticeMailCreated;
use Carbon\Carbon;
use Storage;
use Config;

class NoticesController extends BaseController
{
    protected $key='notices';
    public function __construct(Notices $notices,Files $files,CheckAdmin $checkAdmin)                                          
    {
         $exceptAdmin=[];
         $allowVisitors=[];
		 $this->setMiddleware( $exceptAdmin, $allowVisitors);
		
         $this->notices=$notices;
         $this->files=$files;
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
            
            foreach ($attachments as $attachment) {
                $file_id=0;
                if(array_key_exists('id',$attachment)){
                    $file_id=$attachment['id'];
                } 
                $file_values=[
                    'title' => $attachment['title'],
                    'path' => $attachment['path'],
                    'mime' => $attachment['mime'],
                ];
                $entity=$this->files->createOrUpdate($file_values,$file_id);
                
                $attachment_ids .= $entity->id .',';
                
                $this->files->save_upload_file($entity);
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

            event(new NoticeMailCreated($notice));

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

        $files=$notice->getAttachments();
       
        return response()
            ->json([
                'notice' => $notice,
                'files' => $files
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

        $attachments=$request->getFiles();
        $attachment_ids='';
       
        if($attachments && count($attachments)){
            
            foreach ($attachments as $attachment) {
                $file_id=0;
                if(array_key_exists('id',$attachment)){
                    $file_id=$attachment['id'];
                } 
                $file_values=[
                    'title' => $attachment['title'],
                    'path' => $attachment['path'],
                    'mime' => $attachment['mime'],
                ];
                $entity=$this->files->createOrUpdate($file_values,$file_id);
               
                $attachment_ids .= $entity->id .',';
                
                $this->files->save_upload_file($entity);
           }
        }
        $attachment_ids=Helper::removeLastComma($attachment_ids);
       
        $values['attachments'] = $attachment_ids;

       
        $notice->update($values);
        
        return response()->json(['notice' => $notice ]);
               
    }
    public function email()
    {
        $request = request();
        $notice_id=(int)$request->notice; 

        $mail=\App\Email::where('notice_id',$notice_id)->first();
        $mail->files=$mail->getAttachments();
        
        return response()->json($mail);
        
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
