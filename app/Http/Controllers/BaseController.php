<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Route;
use App\Http\Middleware\CheckAdmin;

class BaseController extends Controller
{
    protected $checkAdmin;
    public function __construct()
    {
          $exceptAdmin=[];
          $allowVisitors=[];
        
		  $this->middleware('admin', ['except' => array_merge($exceptAdmin,$allowVisitors) ]);
        
          
          
          
	}
    protected function setMiddleware(array $exceptAdmin, array $allowVisitors)
    {
        $this->middleware('admin', ['except' => array_merge($exceptAdmin,$allowVisitors) ]);

        if(count($exceptAdmin)) $this->middleware('auth', ['only' => $exceptAdmin]);
        
    }
    protected function setCheckAdmin(CheckAdmin $checkAdmin)
    {
        $this->checkAdmin=$checkAdmin;
    }
    protected function currentUser()
    {
        if($this->checkAdmin){
            return $this->checkAdmin->currentUser();
        }else{
           return request()->user();
        }
        
    }
    protected function currentAdmin()
    {
        if($this->checkAdmin){
            return $this->checkAdmin->getAdmin();
        }else{
           return request()->user()->admin;
        }
        
    }
    

    protected function unauthorized()
    {
        return  response()->json(['msg' => 'from basecontroller 權限不足' ]  ,  401);  
    }
    protected function menus($key)
    {
        $current=Route::current()->uri();
       
        if($key=='signups'){
           return array(
                [
                    'id' => 1,
                    'text' => '報名與繳費',
                    'path' => '/signups',
                    'active' => $current !='signups/new-user'
                ],
                [
                    'id' => 2,
                    'text' => '新學員報名',
                    'path' => '/signups/new-user',
                    'active' => $current=='signups/new-user'
                ],
            );
        } 
        if($key=='refunds'){
           return array(
                [
                    'id' => 1,
                    'text' => '退費管理',
                    'path' => '/refunds',
                    'active' => $current !='refunds/create'
                ],
                [
                    'id' => 2,
                    'text' => '新增退費申請',
                    'path' => '/refunds/create',
                    'active' => $current=='refunds/create'
                ],
            );
        } 
        if($key=='users' || $key=='volunteers' ){
           return array(
                [
                    'id' => 1,
                    'text' => '使用者管理',
                    'path' => '/users',
                    'active' => $current=='users'
                ],
                [
                    'id' => 2,
                    'text' => '新增使用者',
                    'path' => '/users/create',
                    'active' => $current=='users/create'
                ],
                [
                    'id' => 3,
                    'text' => '志工管理',
                    'path' => '/volunteers',
                    'active' => $current=='volunteers'
                ],
                [
                    'id' => 4,
                    'text' => '新增志工',
                    'path' => '/volunteers/create',
                    'active' => $current=='volunteers/create'
                ],
               
            );
        }
        if($key=='teachers'){
           return array(
                [
                    'id' => 1,
                    'text' => '教師管理',
                    'path' => '/teachers',
                    'active' => $current!='teachers/create'
                ],
                [
                    'id' => 2,
                    'text' => '新增教師',
                    'path' => '/teachers/create',
                    'active' => $current=='teachers/create'
                ],
               
            );
        }
        if($key =='courses' || $key =='lessons' || $key =='categories'
            || $key =='admissions'|| $key =='registers'){
           return array(
                [
                    'id' => 1,
                    'text' => '課程管理',
                    'path' => '/courses',
                    'active' => $current=='courses'
                ],
                [
                    'id' => 2,
                    'text' => '新增課程',
                    'path' => '/courses/create',
                    'active' => $current=='courses/create'
                ],
                [
                    'id' => 3,
                    'text' => '錄取名單',
                    'path' => '/admissions',
                    'active' => $current=='admissions'
                ],
                [
                    'id' => 4,
                    'text' => '註冊學員名單',
                    'path' => '/course-registers',
                    'active' => $current=='course-registers'
                ],
                [
                    'id' => 5,
                    'text' => '課堂紀錄表',
                    'path' => '/lessons',
                    'active' => $current=='lessons'
                ],
                [
                    'id' => 6,
                    'text' => '課程分類',
                    'path' => '/categories',
                    'active' => $current=='categories'
                ],
            );
        }
        if($key=='settings'){
           return array(
                [
                    'id' => 1,
                    'text' => '學期設定',
                    'path' => '/terms',
                    'active' => $current=='terms'
                ],
                [
                    'id' => 2,
                    'text' => '假日設定',
                    'path' => '/holidays',
                    'active' => $current=='holidays'
                ],
                [
                    'id' => 3,
                    'text' => '開課中心',
                    'path' => '/centers',
                    'active' => $current=='centers'
                ],
                [
                    'id' => 4,
                    'text' => '教室管理',
                    'path' => '/classrooms',
                    'active' => $current=='classrooms'
                ],
               
                
            );
        }
        if($key=='discounts'|| $key=='identities'){
           return array(
                [
                    'id' => 1,
                    'text' => '折扣設定',
                    'path' => '/discounts',
                    'active' => $current=='discounts'
                ],
                [
                    'id' => 2,
                    'text' => '身分設定',
                    'path' => '/identities',
                    'active' => $current=='identities'
                ],
                
            );
        }

        return [];
    }
}
