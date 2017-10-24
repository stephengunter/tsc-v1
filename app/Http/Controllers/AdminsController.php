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

use Illuminate\Support\Facades\Input;
use DB;

class AdminsController extends BaseController
{
    protected $key='admins';
    public function __construct(Admins $admins, Centers $centers,
                                 Users $users, Roles $roles)
    {   
        
		$this->admins=$admins;
        $this->centers=$centers;
        $this->users=$users;
        $this->roles=$roles;
	}
    public function indexOptions()
    {
        $adminRoles=$this->roles->getAdminRoles()->get();
        $roleOptions=$this->roles->optionsConverting($adminRoles);

        $centerOptions=[];

        if($this->headCenterAdmin()){
            $empty_item=true;
            $centerOptions=$this->centers->options($empty_item);
        }else{

            $centerOptions=$this->centers->optionsConverting($this->canAdminCenters());
        }


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
        $center=(int)request()->get('center');
        if($center){
             $adminList=$this->admins->getByCenter($center);
        }else{
             $adminList=$this->admins->getAll();
        }

        $role=request()->get('role');
        if($role){
            $adminList=$adminList->where('role',$role);
        }

        $active=(int)request()->get('active');
        if($active){
            $adminList=$adminList->where('active',true);
        }else{
            $adminList=$adminList->where('active',false);
        }

        $adminList=$adminList->with('user.profile')->with('centers')->filterPaginateOrder();
        
        foreach ($adminList as $admin) {
           
            $admin->roleModel=$admin->roleModel();
        }

        return response()
            ->json([
                'model' => $adminList,
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

        $current_user=$this->currentUser();

        $centerOptions=[];
        if($this->headCenterAdmin()){           
            $centerOptions=$this->centers->options();
        }else{
            $centerOptions=$this->centers->optionsConverting($this->canAdminCenters());
        }

        $user=null;
        $admin=null;

        if($user_id){
            
            $user=$this->users->findOrFail($user_id);
            if(!$user->canViewBy($current_user)){
                return  $this->unauthorized();     
            }
            
            $user->profile;
            $admin=$user->admin;
            if(!$admin){
                 $admin=Admin::initialize();
                 $admin['role']=$roleOptions[0]['value'];
                 $admin['center_id']=$centerOptions[0]['value'];
            }
           
        }else{
            $user=User::initialize();
            $admin=Admin::initialize();
            $admin['role']=$roleOptions[0]['value'];
            $admin['center_id']=$centerOptions[0]['value'];
        }


        

        return response()
            ->json([
                'user' => $user,
                'admin' => $admin,
                'roleOptions' => $roleOptions,   
                'centerOptions' => $centerOptions,               
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
         $adminId=$request->getAdminId();
         $center_id=$adminValues['center_id'];

         $admin=$this->admins->storeAdmin($userValues,$profileValues,$adminValues,$user_id,$adminId,$current_user,$center_id);

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

        $admin=Admin::with('user.profile')->with('centers')->findOrFail($id);
       
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

        if(!$admin->active){
            event(new AdminDeleted($admin, $current_user));
        }

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

    public function import(Request $form)
    {
        $current_user=$this->currentUser();
        $center_id=0;
        if(!$current_user->isDev()){
            $defaultCenter=$this->defaultCenter();
            if($defaultCenter) $center_id=$defaultCenter->id;
            else return  $this->unauthorized();  
        }


        
        if($current_user->admin){
             $center=$current_user->admin->defaultCenter();
             if($center){
                 $center_id=$center->id;
             }
        }

        if(!$form->hasFile('admins_file')){
            return   response()
                        ->json(['admins_file' => ['無法取得上傳檔案'] 
                            ]  ,  422);      
        }

        $current_user=$this->currentUser();

        $file=Input::file('admins_file');
      

        $this->admins->importAdmins($file,$current_user);

        return response()->json(['success' => true]);

       
    }
    

    



    
   
}
