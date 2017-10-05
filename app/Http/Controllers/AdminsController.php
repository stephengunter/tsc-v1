<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

use App\User;
use App\Profile;
use App\Admin;

use App\Repositories\Admins;
use App\Repositories\Titles;
use App\Repositories\Centers;
use App\Repositories\Users;
use App\Repositories\Roles;

use App\Http\Requests\AdminRequest;
use App\Http\Requests\AdminUserRequest;
use App\Http\Middleware\CheckOwner;

use App\Support\Helper;

use App\Events\AdminCreated;
use App\Events\AdminDeleted;
use App\Events\UserRegistered;


use DB;

class AdminsController extends BaseController
{
    protected $key='admins';
    public function __construct(Admins $admins, Centers $centers,
                                 Users $users, Roles $roles, CheckOwner $checkOwner)
    {   
        $exceptAdmin=[];
        $allowVisitors=[];
		$this->setMiddleware( $exceptAdmin, $allowVisitors,'owner');

        $this->setCheckAdmin($checkOwner);
                 
		$this->admins=$admins;
        $this->centers=$centers;
        $this->users=$users;
        $this->roles=$roles;
	}
    public function indexOptions()
    {
        $adminRoles=$this->roles->getAdminRoles()->get();
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
        if(!request()->ajax()){
            $menus=$this->menus($this->key);            
            return view('admins.index')
                    ->with(['menus' => $menus]);
        }

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
        $request = request();
        $user_id=(int)$request->user;

        if(!$request->ajax()){
            $menus=$this->menus($this->key);            
            return view('admins.create')
                   ->with([ 'menus' => $menus,
                              'id' => $user_id     
                        ]);
        }  

        $adminRoles=$this->roles->getAdminRoles()->get();
        $roleOptions=$this->roles->optionsConverting($adminRoles);

        $user=null;
        $admin=null;

        if($user_id){
            $current_user=$this->currentUser();
            $user=$this->users->findOrFail($user_id);
            if(!$user->canViewBy($current_user)){
                return  $this->unauthorized();     
            }
            
            $user->profile;
            $admin=$user->admin;
            if(!$admin){
                 $admin=Admin::initialize();
                 $admin['role']=$roleOptions[0]['value'];
            }
           
        }else{
            $user=User::initialize();
            $admin=Admin::initialize();
            $admin['role']=$roleOptions[0]['value'];
        }


        

        return response()
            ->json([
                'user' => $user,
                'admin' => $admin,
                'roleOptions' => $roleOptions,               
            ]);

    
    }
    public function store(AdminRequest $request)
    {
         $current_user=$this->currentUser();
         $updated_by=$current_user->id;
         $removed=false;

         $adminValues=$request->getAdminValues($updated_by,$removed);
         $userValues=$request->getUserValues($updated_by,$removed);
         $profileValues=$request->getProfileValues($updated_by,$removed);

         $user_id=$request->getUserId();   
         $is_new_user=true;
         if($user_id)  $is_new_user=false;

         $adminId=$request->getAdminId();

         $user= DB::transaction(function() 
                use($userValues,$profileValues,$adminValues,$user_id,$adminId)
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

                    
                    if($adminId){
                        $admin = Admin::findOrFail($id);
                        $admin->update($adminValues);
                    }else{
                        $admin=$user->admin;
                        if(!$admin){
                            $admin=new Admin($adminValues);  
                            $user->admin()->save($admin);
                        }else{
                            $user->admin->update($adminValues);
                        }
                    }
                 
                    return $user;
                });

         $admin= Admin::findOrFail($user->id);
         event(new AdminCreated($admin, $current_user));

         if($is_new_user){
            event(new UserRegistered($user));            
         }
        
         return response()->json($admin); 
    }

    public function show($id)
    {
        if(!request()->ajax()){
            $menus=$this->menus($this->key);            
            return view('admins.details')
                    ->with([ 'menus' => $menus,
                              'id' => $id     
                        ]);
        }  
        $current_user=$this->currentUser();

        $admin=$this->admins->findOrFail($id);
        $admin->user->profile;
        $canEdit=$admin->canEditBy($current_user);
        $admin->canEdit=$canEdit;
        $admin->canDelete=$canEdit;
        $admin->roleModel=$admin->roleModel();
         
         return response() ->json([ 'admin' => $admin  ]);  
           
                            
           
    }
    public function edit($id)
    {
        $current_user=$this->currentUser();
        $admin=$this->admins->findOrFail($id);
        if(!$admin->canEditBy($current_user)){
            return  $this->unauthorized();    
        }
        $user=$admin->user;
        $user->profile;
        $adminRoles=$this->roles->getAdminRoles()->get();
        $roleOptions=$this->roles->optionsConverting($adminRoles);
       

        return response()
            ->json([
                'user' => $user,
                'admin' => $admin,
                'roleOptions' => $roleOptions,               
            ]);
        
    }
    public function update(Request $request ,$id)
    {
        $current_user=$this->currentUser();
        $admin=$this->admins->findOrFail($id);
        if(!$admin->canEditBy($current_user)){
            return  $this->unauthorized();   
        }

        $updated_by=$current_user->id;

        $request = $request->get('admin');
       
        $adminValues=array_except($request, ['user']);       
        $adminValues=Helper::setUpdatedBy($adminValues,$updated_by);
        $admin->update($adminValues);

        return response()->json($admin);
    }
    public function destroy($id)
    {
        $admin=$this->admins->findOrFail($id);
        $current_user=$this->currentUser();
        if(!$admin->canDeleteBy($current_user)){
            return  $this->unauthorized();  
        }
        $this->admins->delete($id, $current_user->id);

        event(new AdminDeleted($admin, $current_user));

        return response()->json([ 'deleted' => true  ]);
    }
    

    



    
   
}
