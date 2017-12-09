<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\BaseController;

use Illuminate\Http\Request;
use App\Http\Requests\User\UserRequest; 
use App\Http\Requests\User\FindUserRequest;


use App\User;
use App\Profile;

use App\Services\User\UserService;
use DB;

use App\Support\Helper;
use App\Events\UserRegistered;


class UsersController extends BaseController
 {
    protected $key='users';
    
    public function __construct(UserService $userService)                               
    {
          $this->userService=$userService;
          
	}

	public function index()
    {
        if(!request()->ajax()){
            $menus=$this->menus($this->key);            
            return view('users.index')
                    ->with(['menus' => $menus]);
        }  

        $users=$this->userService->getAll()->with('roles')
                                     ->filterPaginateOrder();
        return response()->json([ 'model' => $users ]);
                
                    
               
    }

    public function create()
    {
        if(!request()->ajax()){
            $menus=$this->menus($this->key);            
            return view('users.create')
                    ->with(['menus' => $menus]);
        }  

        $role=request()->get('role');

        if($role) $with_password=false;
        else $with_password=true;
        
        $user= User::initialize($with_password,$role);
        
        return response()->json(['user' => $user ]);
            
                
                
           
    }

    

    public function store(UserRequest $request)
    {
        
        $current_user=$this->currentUser();
        $updated_by=$current_user->id;

        $userValues=$request->getUserValues($updated_by);
        $user=new User($userValues);
        

        $profileValues=$request->getProfileValues($updated_by);
        $profile=new Profile($profileValues);   
        
        $user=$this->userService->store($user, $profile);
        

        return response()->json($user);
        
            
    }

    public function show($id)
    {
        
        if(!request()->ajax()){
            $active=request()->get('active'); 

            $menus=$this->menus($this->key);            
            return view('users.details')
                    ->with([ 'menus' => $menus,
                              'id' => $id  ,
                              'active' => $active   
                        ]);
        }  

        $current_user=$this->currentUser();
        
        $user = User::with('profile','roles')->findOrFail($id);
        if(!$user->canViewBy($current_user)){
            return  $this->unauthorized();   
        }
        

        $user->defaultRole=$user->defaultRole();
        $user->canEdit=$user->canEditBy($current_user);
        $user->canDelete=$user->canDeleteBy($current_user);
        
        return response()->json(['user' => $user]);
            
                
            
    }

    public function edit($id)
    {        
        $current_user=$this->currentUser();

        $user = User::with('profile')->findOrFail($id);
        if(!$user->canEditBy($current_user)){
            return  $this->unauthorized();       
        }
        // $user->defaultRole=$user->defaultRole();
        // $titleOptions=$this->titles->options();
        return response()
                ->json([
                    'user' => $user,
                    // 'titleOptions' => $titleOptions
                ]);
        
    }

    public function update(UserRequest $request, $id)
    {
        
        $current_user=$this->currentUser();

        $user = User::findOrFail($id);
        if(!$user->canEditBy($current_user)){
            return  $this->unauthorized();       
        }
        $updated_by=$current_user->id;
        $removed=false;
        $userValues=$request->getUserValues($updated_by,$removed);       
        $profileValues=$request->getProfileValues($updated_by);
       
        $user= $this->userService->update($userValues,$profileValues, $user);
        
        return response()->json($user);
    }

    public function destroy($id)
    {
        $current_user=$this->currentUser();
        
        $deleted=$this->userService->delete($id ,$current_user);
        if(!$deleted){
            return  $this->unauthorized();  
        }

        return response()->json(['deleted' => true]);
            
    }
   
    public function rolesCanAdd($id)
    {
        $user =User::findOrFail($id);

        $current_user=$this->currentUser();
        $with_admin=$current_user->isOwner();
        $roles=$user->rolesCanAdd($with_admin);

        return response()->json([ 'roles' => $roles ]);
    }
    public function roles($id)
    {
        $user =User::findOrFail($id);
        
        return response()->json([ 'roles' => $user->roles ]);
           
    }

    public function updateContactInfo(Request $request, $userId)
    {
        $user =User::findOrFail($id);
        $current_user=$this->currentUser();
        if(!$user->canEditBy($current_user)){
              return  $this->unauthorized();
        }

        $user->contact_info=$request['contact_info'];
        $user->updated_by=$current_user->id;
        $user->save();

        return response()->json(['saved' => true ]);

    }
    public function updatePhoto(Request $request, $userId)
    {
        $user =User::findOrFail($id);

        $current_user=$this->currentUser();

        
        if(!$user->canEditBy($current_user)){
              return  $this->unauthorized();
        }
       
        $user->profile->photo_id=$request['photo_id'];
        $user->profile->updated_by=$current_user->id;
        $user->profile->save();
           
        return response()->json(['saved' => true ]);

    }

    public function findUsers(FindUserRequest $request)
    {
        $values=$request->getValues();
        $email=$values['email'];
        $phone=$values['phone'];

        $userList=$this->userService->findUsers($email, $phone);
        
        
        return response()->json(['userList' => $userList  ]);  
                  
    }

    
	
}
