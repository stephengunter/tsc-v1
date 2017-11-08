<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\BaseController;

use App\Http\Requests\Teacher\TeacherRequest;
use Illuminate\Http\Request;

use App\Center;
use App\Teacher;
use App\User;
use App\Profile;
use App\Address;

use App\Repositories\Teachers;
use App\Repositories\Centers;

use App\Support\Helper;

use Illuminate\Support\Facades\Input;

class TeachersReviewController extends BaseController
 {
    protected $key='teachers';

    public function __construct(Teachers $teachers ,Centers $centers)                                                               
    {
        $this->teachers=$teachers; 
        $this->centers=$centers; 
         
    }
    private function centersCanReview($currentUser)
    {
        if($currentUser->isDev()) return Center::where('removed',false)->get();

        return $currentUser->admin->validCenters();
    }
	public function index()
    {
        $current_user=$this->currentUser();
        $centersCanReview=$this->centersCanReview($current_user);
        
        if(!request()->ajax()){
            $centerOptions=$this->centers->optionsConverting($centersCanReview);
            $menus=$this->menus($this->key);   
            return view('teachers.review')->with([ 
                'menus' => $menus ,
                'centerOptions' => $centerOptions
            ]);          
            
        }  

        $request = request();
       
        $centerId=(int)$request->center; 

        if(!$centerId) abort(404);
        

        $teacherList=$this->teachers->getByCenter($centerId)->with('user.profile')
                                    ->where('reviewed',false) //->orderBy('updated_at','desc')
                                    ->filterPaginateOrder();
                                    

       
        foreach($teacherList as $teacher){
            $teacher->centerNames=$teacher->centerNames();
        }
        
                                    
        return response()->json([ 'model' => $teacherList  ]);           
           
    }


  
    public function store(Request $form)
    {
        $current_user=$this->currentUser();
        $teacher_ids=$form['teacher_ids'];
        
        $reviewed=true;
        if(count($teacher_ids)){
            for($i = 0;  $i< count($teacher_ids);  ++$i) {
                $id=$teacher_ids[$i];
                $this->teachers->updateReview($id,$reviewed,$current_user);
            }
        }
       
        return response()->json(['success' => true ]); 

        
    }

    
    public function update(Request $form,$id)
    {
        $current_user=$this->currentUser();
        $teacher = Teacher::findOrFail($id);
        if(!$teacher->canReviewBy($current_user)){
            return  $this->unauthorized();    
        }

        
        $reviewed=$form['reviewed'];

        $current_user=$this->currentUser();
       
        $teacher=$this->teachers->updateReview($id,$reviewed,$current_user);    

        return response()->json($teacher); 
         
           
    }
    

	
}
