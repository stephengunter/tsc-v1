<?php

namespace App\Http\Controllers\User;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Volunteer;
use App\User;
use App\Profile;

use App\Repositories\Volunteers;
use App\Repositories\Titles;
use App\Repositories\Centers;
use App\Repositories\Users;
use App\Repositories\Roles;
use App\Http\Requests\User\VolunteerRequest;
use App\Http\Requests\VolunteerUserRequest;

use App\Http\Middleware\CheckAdmin;
use App\Support\Helper;

use App\Events\VolunteerCreated;
use App\Events\VolunteerDeleted;

use DB;

class VolunteersController extends BaseController
{
    protected $key='volunteers';

    public function __construct(Volunteers $volunteers,Centers $centers, Users $users)                 
    {
        

		$this->volunteers=$volunteers;
        $this->centers=$centers;
        $this->users=$users;
         
	}
    
    public function index()
    {
        if(!request()->ajax()){
            $menus=$this->menus($this->key);            
            return view('volunteers.index')
                    ->with(['menus' => $menus]);
        }

        $request = request();
        
        
        $volunteers=$this->volunteers->getAll()
                                    ->with('user')
                                    ->with('user.profile')
                                    ->filterPaginateOrder();

        
                                    
        return response()->json([ 'model' => $volunteers    ]); 
            
                              
          

    }

    public function create()
    {
        $request = request();
        $user_id=(int)$request->user;
         
        if(!$request->ajax()){
            $menus=$this->menus($this->key);            
            return view('volunteers.create')
                   ->with([ 'menus' => $menus,
                              'id' => $user_id     
                        ]);
        }  

        $user=null;
        $volunteer=null;

        if($user_id){
           
            $user=User::findOrFail($user_id);
           
            
            $user->profile;
            $volunteer=$user->volunteer;
            if(!$volunteer){
                 $volunteer=Volunteer::initialize();
            }
           
        }else{
             $user=User::initialize();
             $volunteer=Volunteer::initialize();
        }

        return response()
            ->json([
                'user' => $user,
                'volunteer' => $volunteer           
            ]);
       
       
    }
    public function store(VolunteerRequest $request)
    {
         $current_user=$this->currentUser();
         $updated_by=$current_user->id;

         $volunteerValues=$request->getVolunteerValues($updated_by);
         $userValues=$request->getUserValues($updated_by);
         $profileValues=$request->getProfileValues($updated_by);

         $user_id=$request->getUserId();
         $volunteerId=$request->getVolunteerId();

         $volunteer=$this->volunteers->storeVolunteer($userValues,$profileValues,$volunteerValues,$user_id,$volunteerId);

       
         return response()->json($volunteer); 
    }

    public function show($id)
    {   
        if(!request()->ajax()){
            $menus=$this->menus($this->key);            
            return view('volunteers.details')
                    ->with([ 'menus' => $menus,
                              'id' => $id     
                        ]);
        }  
        $current_user=$this->currentUser();
        $volunteer=$this->volunteers->findOrFail($id);
        if(!$volunteer->canViewBy($current_user)){
           return  $this->unauthorized();
        }
        
        $volunteer->canEdit=$volunteer->canEditBy($current_user);
        $volunteer->canDelete=$volunteer->canDeleteBy($current_user);

        $user=$volunteer->user;
        $user->profile->titleText=$user->profile->titleText();
        
       
        return response()
            ->json([
                'volunteer' => $volunteer                
            ]);
    }

    public function edit($id)
    {
        $current_user=$this->currentUser();
        $volunteer=$this->volunteers->findOrFail($id);
        if(!$volunteer->canEditBy($current_user)){
            return  $this->unauthorized();    
        }
        $user=$volunteer->user;
        $user->profile->titleText=$user->profile->titleText();
        $titleOptions=$this->titles->options();
        return response()->json([
            'volunteer' => $volunteer,
            'titleOptions' => $titleOptions
        ]);
        
    }
    public function update(Request $request ,$id)
    {
        $current_user=$this->currentUser();
        $volunteer=$this->volunteers->findOrFail($id);
        if(!$volunteer->canEditBy($current_user)){
            return  $this->unauthorized();   
        }

        $updated_by=$current_user->id;

        $request = $request->get('volunteer');
       
        $volunteerValues=array_except($request, ['user']);       
        $volunteerValues=Helper::setUpdatedBy($volunteerValues,$updated_by);
        $volunteer->update($volunteerValues);

        $title_id=$request['user']['profile']['title_id'];
        $profile=Profile::findOrFail($id);
        $profile->title_id=$title_id;
        $profile->save();

        return response()->json($volunteer);
    }
    public function destroy($id)
    {
        $volunteer=$this->volunteers->findOrFail($id);
        $current_user=$this->currentUser();
        if(!$volunteer->canDeleteBy($current_user)){
            return  $this->unauthorized();  
        }
        $this->volunteers->delete($id, $current_user->id);

        event(new VolunteerDeleted($volunteer, $current_user));

        return response()->json([ 'deleted' => true  ]);
    }

   
     
}
