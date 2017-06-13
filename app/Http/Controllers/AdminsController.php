<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Repositories\Admins;
use App\Repositories\Titles;
use App\Repositories\Centers;
use App\Repositories\Users;
use App\Repositories\Roles;

use App\Http\Requests\AdminRequest;
use App\Http\Requests\AdminUserRequest;
use App\Http\Middleware\CheckOwner;

use App\Support\Helper;

class AdminsController extends Controller
{
    
    public function __construct(Admins $admins, Centers $centers,
                                 Users $users, Roles $roles, CheckOwner $checkOwner)
    {   
        $exceptOwner=['updateUser'];
		$this->middleware('owner', ['except' => $exceptOwner ]);       
        $this->middleware('auth', ['only' => $exceptOwner]);
        $this->checkOwner=$checkOwner;
       
                 
		$this->admins=$admins;
        $this->centers=$centers;
        $this->users=$users;
        $this->roles=$roles;
	}
    public function indexOptions()
    {
        $adminRoles=$this->roles->getAdminRoles();
        $roleOptions=$this->roles->optionsConverting($adminRoles);

        $centerOptions=$this->centers->options();
        return response()
            ->json([
                'roleOptions' => $roleOptions,
                'centerOptions' => $centerOptions
            ]);

    }

    public function index()
    {
        $adminList=[];
        $center=intval(request()->get('center'));
        if($center){
            $adminList=$this->admins->getByCenter($center);
        }else{
             $adminList=$this->admins->getAll();
        }

        $role=request()->get('role');
        if($role){
            $adminList=$adminList->where('role',$role);
        }

        $ids=$adminList->select('user_id')->get()->pluck('user_id')->toArray();
        
        $users= $this->users->getByIds($ids)->with('profile')-> filterPaginateOrder();
        
        foreach ($users as $user) {
            $user->admin->centers;
            $user->admin->roleModel=$user->admin->roleModel();
        }

        return response()
            ->json([
                'model' => $users,
            ]);
    }

    public function create()
    {
       $user=request()->get('user');
       if(!$user) abort(404);

       $user=$this->users->findOrFail($user);
       $user->profile;
       $admin=$this->admins->initialize($user->id);
      
       $adminRoles=$this->roles->getAdminRoles()->get();
       $roleOptions=$this->roles->optionsConverting($adminRoles);

       $centers=$this->checkOwner->centersCanAdmin();       
       $centerOptions=$this->centers->optionsConverting($centers);

       return response()
            ->json([
                'user' => $user,
                'admin' => $admin,
                'roleOptions' => $roleOptions,
                'centerOptions' => $centerOptions
            ]);
    }
    public function store(AdminRequest $request)
    {
         $current_user=$this->checkOwner->getOwner();
         $user_id=$request->getUserId();
         $user=$this->users->findOrFail($user_id);
         if(!$user->canEditBy($current_user)){
            return   response()->json(['msg' => '權限不足' ]  ,  401);    
         }

         $updated_by=$current_user->id;
         $removed=false;
         $userValues=$request->getUserValues($updated_by,$removed);       
         $profileValues=$request->getProfileValues($updated_by,$removed);
         $adminValues=$request->getAdminValues($updated_by,$removed);

         $user= $this->users->updateUserAndProfile($userValues,$profileValues, $user);

         $centerIds = $request->getCenterIds();
         $user= $this->admins->store($user,$adminValues,$centerIds);

         $this->admins->addToRole($user->id);
          
          
          return response()->json([
                'user' => $user
            ]); 
    }

    public function show($id)
    {
         $current_user=$this->checkOwner->getOwner();

         $admin=$this->admins->findOrFail($id);
         
         $canEdit=$admin->canEditBy($current_user);
         $admin->canEdit=$canEdit;
         $admin->canDelete=$canEdit;
         $admin->roleModel=$admin->roleModel();
         
         return response()
            ->json([
                'admin' => $admin                
            ]);
    }
    public function update(Request $request ,$id)
    {
         $values=$request['admin'];
       
         $admin=$this->admins->update($id, $values);

         return response()
            ->json([
                'admin' => $admin                
            ]);
    }
    public function destroy($id)
    {
       $this->admins->delete($id);

        return response()
            ->json([
                'deleted' => true
            ]);
    }
    public function updateUser(AdminUserRequest $request, $id)
    {
         $current_user=request()->user();  
         $user=$this->users->findOrFail($id);
         if(!$user->canEditBy($current_user)){
            return   response()->json(['msg' => '權限不足' ]  ,  401);      
         }
         $removed=false;
         $updated_by=$current_user->id;
         $userValues=$request->getUserValues($updated_by,$removed);       
         $profileValues=$request->getProfileValues($updated_by);
         $user= $this->users->updateUserAndProfile($userValues,$profileValues, $user);
        
         return response()->json([
                'user' => $user
            ]); 
    }

    



    
   
}
