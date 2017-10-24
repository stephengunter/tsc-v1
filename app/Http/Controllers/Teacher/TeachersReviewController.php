<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\BaseController;

use App\Http\Requests\Teacher\TeacherRequest;
use Illuminate\Http\Request;

use App\Teacher;
use App\User;
use App\Profile;
use App\Address;

use App\Repositories\Teachers;
use App\Repositories\Courses;
use App\Repositories\Users;
use App\Repositories\Centers;
use App\Repositories\Roles;
use App\Http\Middleware\CheckAdmin;
use App\Support\Helper;

use Illuminate\Support\Facades\Input;

use DB;

use Storage;

class TeachersReviewController extends BaseController
 {
    protected $key='teachers';

    public function __construct(Teachers $teachers ,Users $users, Courses $courses,
                                Centers $centers)                               
    {
        
        
        $this->teachers=$teachers;
        $this->addressRepo=$teachers;       
        $this->users=$users;  
        $this->courses=$courses; 
        $this->centers=$centers; 

       
         
	}
	public function index()
    {
       
        if(!request()->ajax()){
            $menus=$this->menus($this->key);            
            return view('teachers.review')
                    ->with(['menus' => $menus]);
        }  
        $request = request();
        
        $centerId=(int)$request->center; 

        $teacherList=[];
        $centerOptions=[];
        if($centerId){
          
            $teacherList=$this->teachers->getByCenter($centerId)
                                        ->with('user.profile');
                                       
        }else{
            $centerOptions=$this->centers->optionsConverting($this->canAdminCenters());
            if(count($centerOptions)){
                $centerId=$centerOptions[0]['value'];
            }
            
            
        }

        $teacherList=$this->teachers->getByCenter($centerId)
        ->where('reviewed',false)->with('user.profile')
        ->orderBy('updated_at','desc')->get();
        

       
        if(count($teacherList)){
            foreach($teacherList as $teacher){
                $teacher->centerNames=$teacher->centerNames();
            }
        }
        
                                    
        return response()
            ->json([
               'teacherList' => $teacherList,
               'centerOptions' => $centerOptions             
            ]);
    }


  
    public function store(Request $form)
    {
        $current_user=$this->currentUser();
        $teacher_ids=$form['teachers'];
        $reviewed=true;
        if(count($teacher_ids)){
            for($i = 0;  $i< count($teacher_ids);  ++$i) {
                $id=$teacher_ids[$i];
                $this->teachers->updateReview($id,$reviewed,$current_user);
            }
        }

        
       
        return response()->json(['success' => true ]); 

        
    }

    

    

	
}