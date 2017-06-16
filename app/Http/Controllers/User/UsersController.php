<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\BaseController;

use App\Http\Middleware\CheckAdmin;

use Illuminate\Http\Request;
use App\Http\Requests\User\UserRequest; 
use App\Http\Requests\User\FindUserRequest;


use App\User;
use App\Profile;

use App\Repositories\Users;
use App\Repositories\Titles;
use App\Repositories\Registrations;
use DB;

use App\Support\Helper;
use App\Events\UserRegistered;


class UsersController extends BaseController
 {
    protected $key='users';
    
    public function __construct(Users $users, Titles $titles, 
                                Registrations $registrations, CheckAdmin $checkAdmin)
    {
        //   $exceptAdmin=['show','edit','update','updateContactInfo','updatePhoto'];
          $exceptAdmin=[];
          $allowVisitors=[];

          $this->setMiddleware( $exceptAdmin, $allowVisitors);
        
          $this->users=$users;
          $this->titles=$titles;
         
          $this->registrations=$registrations;

          $this->setCheckAdmin($checkAdmin);
          
	}

	public function index()
    {
         
        if(!request()->ajax()){
            $menus=$this->menus($this->key);            
            return view('users.index')
                    ->with(['menus' => $menus]);
        }  

        $users=$this->users->getAll()->with('roles');
        return response()
            ->json([
                'model' => $users -> filterPaginateOrder()
            ]);
    }

    public function create()
    {
        if(!request()->ajax()){
            $menus=$this->menus($this->key);            
            return view('users.create')
                    ->with(['menus' => $menus]);
        }  

        $with_password=true;
        $user= $this->users->initialize($with_password);
        
        return response()
            ->json([
                'user' => $user,
                'option' => []
            ]);
    }

    

    public function store(UserRequest $request)
    {
        
        $admin_id=$this->checkAdmin->getAdminId();

        $removed=false;
        $updated_by=$admin_id;

        $userValues=$request->getUserValues($updated_by,$removed);
        
        if(!$userValues['email'] && !$userValues['phone'] ){
            abort(404);
        }

        $profileValues=$request->getProfileValues($updated_by);        
        
        $user= DB::transaction(function() 
        use($userValues,$profileValues){
              $user=User::create($userValues);
              $profile=new Profile($profileValues);
              $user->profile()->save($profile);

              return $user;
              
        });
      
        event(new UserRegistered($user));

        return response()->json($user);
            
    }

    public function show($id)
    {
        if(!request()->ajax()){
            $menus=$this->menus($this->key);            
            return view('users.details')
                    ->with([ 'menus' => $menus,
                              'id' => $id     
                        ]);
        }  

        $current_user=request()->user();
        
        $user = User::with('profile.title','roles')->findOrFail($id);
        if(!$user->canViewBy($current_user)){
            return  $this->unauthorized();   
        }

        if($user->admin){
           $user->admin->validCenters = $user->admin->validCenters();
        }
        

        $user->defaultRole=$user->defaultRole();
        $user->canEdit=$user->canEditBy($current_user);
        $user->canDelete=$user->canDeleteBy($current_user);
        
        return response()
            ->json([
                'user' => $user
            ]);
    }

    public function edit($id)
    {
        $current_user=request()->user();
        $user = User::with('profile')->findOrFail($id);
        if(!$user->canEditBy($current_user)){
            return  $this->unauthorized();       
        }
        $user->defaultRole=$user->defaultRole();
        $titleOptions=$this->titles->options();
        return response()
                ->json([
                    'user' => $user,
                    'titleOptions' => $titleOptions
                ]);
        
    }

    public function update(UserRequest $request, $id)
    {
        
         $current_user=request()->user();        
         $user = User::findOrFail($id);
         if(!$user->canEditBy($current_user)){
            return  $this->unauthorized();       
         }
         $updated_by=$current_user->id;
         $removed=false;
         $userValues=$request->getUserValues($updated_by,$removed);       
         $profileValues=$request->getProfileValues($updated_by);
       
         $user= $this->users->updateUserAndProfile($userValues,$profileValues, $user);
        
         return response()->json($user);
    }

    public function destroy($id)
    {
        $current_user=$this->checkAdmin->getAdmin();
        
        $deleted=$this->users->delete($id ,$current_user);
        if(!$deleted){
            return  $this->unauthorized();  
        }

        return response()
            ->json([
                'deleted' => true
            ]);
    }
   
    public function rolesCanAdd($id)
    {
        $user =$this->users->findOrFail($id);
        
        $with_admin=$this->checkAdmin->isOwner();
         return response()
            ->json([
                'roles' => $user->rolesCanAdd($with_admin)
            ]);
    }
    public function roles($id)
    {
        $user =$this->users->findOrFail($id);
        
         return response()
            ->json([
                'roles' => $user->roles
            ]);
    }

    public function updateContactInfo(Request $request, $userId)
    {
        $user =$this->users->findOrFail($userId);

        $current_user=request()->user();
        if(!$user->canEditBy($current_user)){
              return  $this->unauthorized();
        }

        $user->contact_info=$request['contact_info'];
        $user->updated_by=$current_user->id;
        $user->save();

            return response()
                    ->json([
                        'saved' => true
                    ]);

    }
    public function updatePhoto(Request $request, $userId)
    {
        $user =$this->users->findOrFail($userId);

        $current_user=request()->user();
        if(!$user->canEditBy($current_user)){
              return  $this->unauthorized();
        }
       
        $user->profile->photo_id=$request['photo_id'];
        $user->profile->updated_by=$current_user->id;
        $user->profile->save();
           
            return response()
                    ->json([
                        'saved' => true
                    ]);


    }

    public function findUsers(FindUserRequest $request)
    {
        $values=$request->getValues();
        $email=$values['email'];
        $phone=$values['phone'];
        
        $allUsers = $this->users->getAll();
        $userList=[];
        $users=[];
        if(!$email){
            $users = $allUsers->where('phone',$phone);
        }else{
            $users = $allUsers->where('phone',$phone)
                    ->orWhere('email',$email);
        };

        if($users->count()){
            $userList=$users->with('profile')->get();
        }
        return response()
                    ->json([
                        'userList' => $userList
                    ]);
    }

    
	
}
