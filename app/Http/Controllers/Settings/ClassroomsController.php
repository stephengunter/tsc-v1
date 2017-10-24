<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Http\Requests\Settings\ClassroomRequest;

use App\Classroom;

use App\Repositories\Classrooms;
use App\Repositories\Centers;
use App\Http\Middleware\CheckAdmin;

use App\Support\Helper;

class ClassroomsController extends BaseController
{
    protected $key='settings';
    public function __construct(Classrooms $classrooms, Centers $centers )                                
    {
       
		$this->classrooms=$classrooms;
        $this->centers=$centers;
         
        
	}

    public function index()
    {
        if(!request()->ajax()){
            
            $menus=$this->menus($this->key);            
            return view('settings.classroom')
                    ->with(['menus' => $menus]);  
                    
        }

        $centerOptions=[];
        $classroomList=[];
        
        $center=(int)request()->get('center');

        if(!$center){

            $current_user=$this->currentUser();
            $centers_can_admin=$current_user->admin->centersCanAdmin();
            if(!$centers_can_admin)  return  $this->unauthorized();

            $centerOptions=$this->centers->optionsConverting($centers_can_admin);

            $center=$centerOptions[0]['value'];
        }

        $withCenter=true;
        $classroomList = $this->classrooms->getByCenter($center,$withCenter)
                                            ->orderBy('active','desc')->get();

        if(count($classroomList)){
            $current_user=$this->currentUser();
            foreach ($classroomList as $classroom) {
                $classroom->canEdit=$classroom->canEditBy($current_user);
                $classroom->canDelete=$classroom->canDeleteBy($current_user);
            }
        }

        if(count($centerOptions)){
            return response()->json([
                                        'centerOptions' => $centerOptions,
                                        'classroomList' => $classroomList
                                    ]);   
        }else{
            return response() ->json([ 'classroomList' => $classroomList ]);
        }
       
       
           
                  
    }
    
    public function create()
    {
        $classroom=Classroom::initialize();
        return response() ->json([ 'classroom' => $classroom ]);
    }
   
    public function store(ClassroomRequest $request)
    {
        $current_user=$this->currentUser();
        $updated_by=$current_user->id;
        $removed=false;
        $values=$request->getValues($updated_by,$removed);

        $center_id=$values['center_id'];
        $center=$this->centers->findOrFail($center_id);
        $canAdmin=$current_user->admin->canAdminCenter($center);
        if(!$canAdmin){
            return  $this->unauthorized();    
        }

        $classroom=$this->classrooms->store($values);

        return response()->json(['classroom' => $classroom ]);          
      
    }
    public function edit($id)
    {
        $current_user=$this->currentUser();
        $classroom=Classroom::findOrFail($id); 
        if(!$classroom->canEditBy($current_user)){
            return  $this->unauthorized();    
        }
      
        return response()->json([
            'classroom' => $classroom
        ]);
        
    }
    
    public function update(ClassroomRequest $request, $id)
    {
        $current_user=$this->currentUser();
        $classroom=Classroom::findOrFail($id); 
        if(!$classroom->canEditBy($current_user)){
            return  $this->unauthorized();    
        }

        $updated_by=$current_user->id;
        $removed=false;
        $values=$request->getValues($updated_by,$removed);

        $center_id=$values['center_id'];
        $center=$this->centers->findOrFail($center_id);
        $canAdmin=$current_user->admin->canAdminCenter($center);
        if(!$canAdmin){
            return  $this->unauthorized();    
        }

        $classroom=$this->classrooms->update($values,$id);

         return response() ->json($classroom);
    }
    public function destroy($id)
    {
        $classroom = $this->classrooms->findOrFail($id);
        $current_user=$this->currentUser();
        if(!$classroom->canDeleteBy($current_user)){
             return  $this->unauthorized();
        }

        $this->classrooms->delete($id ,$current_user->id);

        return response() ->json([ 'deleted' => true ]);
    }


    public function options($center)
    {
        

        $options=$this->classrooms->options($center);
      
          return response()
            ->json([
                'options' => $options
            ]);
    }
   
}
