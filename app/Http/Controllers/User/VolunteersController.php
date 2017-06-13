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
use App\Http\Requests\VolunteerRequest;
use App\Http\Requests\VolunteerUserRequest;

use App\Http\Middleware\CheckAdmin;
use App\Support\Helper;

use App\Events\VolunteerCreated;
use App\Events\VolunteerDeleted;

use DB;

class VolunteersController extends BaseController
{
    protected $key='volunteers';

    public function __construct(Volunteers $volunteers, Titles $titles, 
                        Centers $centers, Users $users, CheckAdmin $checkAdmin)
    {
        $exceptAdmin=[];
        $allowVisitors=[];
        $this->setMiddleware( $exceptAdmin, $allowVisitors);

		$this->volunteers=$volunteers;
        $this->titles=$titles;
        $this->centers=$centers;
        $this->users=$users;

        $this->setCheckAdmin($checkAdmin);
         
	}

    
    public function index()
    {
        if(!request()->ajax()){
            $menus=$this->menus($this->key);            
            return view('volunteers.index')
                    ->with(['menus' => $menus]);
        }

        $request = request();
        
        $centerId=(int)$request->center;
        $volunteerList=[];
        if($centerId){
          
            $volunteerList=$this->volunteers->getByCenter($centerId)
                                        ->with('user')->with('user.profile');
                                       
        }else{
            $volunteerList=$this->volunteers->getAll()
                                        ->with('user')->with('user.profile');
        }

      
        $volunteerList=$volunteerList->filterPaginateOrder();   

        foreach($volunteerList as $volunteer){
            $volunteer->centerNames=$volunteer->centerNames();
            $user=$volunteer->user;
            $user->profile->titleText=$user->profile->titleText();
        }
                                    
        return response()
            ->json([
               'model' => $volunteerList                
            ]); 

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
            $current_user=$this->currentUser();
            $user=$this->users->findOrFail($user_id);
            if(!$user->canViewBy($current_user)){
                return  $this->unauthorized();     
            }

            
            $user->profile;
            $volunteer=$user->volunteer;
            if(!$volunteer){
                 $volunteer=Volunteer::initialize();
            }
           
        }else{
             $user=User::initialize();
             $volunteer=Volunteer::initialize();
        }


        $titleOptions=$this->titles->options();

        return response()
            ->json([
                'user' => $user,
                'volunteer' => $volunteer,
                'titleOptions' => $titleOptions,               
            ]);
       
       
    }
    public function store(VolunteerRequest $request)
    {
         $current_user=$this->currentUser();
         $updated_by=$current_user->id;
         $removed=false;

         $volunteerValues=$request->getVolunteerValues($updated_by,$removed);
         $userValues=$request->getUserValues($updated_by,$removed);
         $profileValues=$request->getProfileValues($updated_by,$removed);

         $user_id=$request->getUserId();
         $volunteerId=$request->getVolunteerId();

         $user= DB::transaction(function() 
                use($userValues,$profileValues,$volunteerValues,$user_id,$volunteerId)
                {
                    $user=null;
                    if($user_id){
                        $user=User::findOrFail($user_id);
                        $user->update($userValues);
                        $user->profile->update($profileValues);
                    }else{
                        $user=new User($userValues);
                        $user->password= config('app.default_password');
                        $user->save();
                        $profile=new Profile($profileValues);
                        $user->profile()->save($profile);
                    }

                    
                    if($volunteerId){
                        $volunteer = Volunteer::findOrFail($id);
                        $volunteer->update($volunteerValues);
                    }else{
                        $volunteer=$user->volunteer;
                        if(!$volunteer){
                            $volunteer=new Volunteer($volunteerValues);  
                            $user->volunteer()->save($volunteer);
                        }else{
                            $user->volunteer->update($volunteerValues);
                        }
                    }
                 
                    return $user;
                });

         $volunteer= Volunteer::findOrFail($user->id);
         event(new VolunteerCreated($volunteer, $current_user));
        
       
       
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

        return response()
            ->json([
                'deleted' => true
            ]);
    }

   
     
}
