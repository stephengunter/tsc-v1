<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Route;

use App\Center;

use Illuminate\Auth\AuthenticationException;

class BaseController extends Controller
{
    
    protected function currentUser()
    {        
        return auth()->user();
    }
    protected function headCenterAdmin()
    {   
        $currentUser=$this->currentUser();
        if($currentUser->isDev())  return true;
            
        $admin=auth()->user()->admin;
        return $admin->fromHeadCenter();
    }
    protected function canAdminCenters()
    {   

        $currentUser=$this->currentUser();
        if(!$currentUser) return [];    

        if($currentUser->isDev()) return Center::where('removed',false)
                                                ->where('oversea',false)->get();


        $admin=$currentUser->admin;
        if(!$admin) return [];  
        return $admin->centersCanAdmin();
        
        
    }
    protected function canAdminCenter($center)
    {   
        return $this->canAdminCenters()->contains($center);
    }
    protected function canAdminCreditCourse()
    {   
        $canAdminCenters = $this->canAdminCenters();

        $canAdminCenterCodes =array_map('strtolower', $canAdminCenters->pluck('code')->toArray());
        $credit_center_codes=array_map('strtolower' , config('app.course.credit_centers'));

        $intersect= array_intersect($canAdminCenterCodes,$credit_center_codes);
       
        if(count($intersect)) return true;
        return false;
        
    }
    protected function defaultCenter()
    {   
        $currentUser=$this->currentUser();     
        if($currentUser->isDev()){
            return Center::getHeadCenter();
        }
        $admin=$currentUser->admin;
        if($admin) return $admin->defaultCenter();
        return null;
        
    }


    protected function unauthorized()
    {
         throw new AuthenticationException();
    }

    protected function requestError($key,$msg)
    {
        return   response()->json([ $key =>  [$msg] ]  ,  422);
       
    }


    protected function menus($key)
    {
        $current=Route::current()->uri();
        $current_user=$this->currentUser();
       
        if($key=='signups'){
           return array(
                [
                    'id' => 1,
                    'text' => '報名與繳費',
                    'path' => '/signups',
                    'active' => $current =='signups'
                ],
                [
                    'id' => 2,
                    'text' => '新學員報名',
                    'path' => '/signups/new-user',
                    'active' => $current=='signups/new-user'
                ],
                [
                    'id' => 3,
                    'text' => '折扣設定',
                    'path' => '/discounts',
                    'active' => $current=='discounts'
                ]
                
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
                    'active' => $current=='volunteers' || $current=='volunteers/create'
                ],
                [
                    'id' => 4,
                    'text' => '稱謂設定',
                    'path' => '/titles',
                    'active' => $current=='titles'
                ],
               
            );
        }
        if($key=='teachers'){
           return array(
                [
                    'id' => 1,
                    'text' => '教師管理',
                    'path' => '/teachers',
                    'active' => $current=='teachers',
                    'items' => array(
                        [
                            'text' => '一般教師',
                            'path' => '/teachers',
                        ],
                        [
                            'text' => '教師群組',
                            'path' => '/teachers?group=true',
                        ]

                    )
                ],
                // [
                //     'id' => 2,
                //     'text' => '新增教師',
                //     'path' => '/teachers/create',
                //     'active' => $current=='teachers/create',
                    
                // ],
                [
                    'id' => 3,
                    'text' => '匯入教師',
                    'path' => '/teachers-import',
                    'active' => $current=='teachers-import',
                    
                ],
                [
                    'id' => 4,
                    'text' => '教師審核',
                    'path' => '/teachers-review',
                    'active' => $current=='teachers-review',
                    
                ],
               
            );
        }
        if($key =='courses')
        {
           return array(
                [
                    'id' => 1,
                    'text' => '課程管理',
                    'path' => '/courses',
                    'active' => $current=='courses'
                ],
                [
                    'id' => 2,
                    'text' => '課程審核',
                    'path' => '/courses-review',
                    'active' => $current=='courses-review'
                ],
                [
                    'id' => 3,
                    'text' => '匯入課程',
                    'path' => '/courses-import',
                    'active' => $current=='courses-import',
                    // 'items' => array(
                    //     [
                    //         'text' => '從舊課程複製',
                    //         'path' => '/courses-import',
                    //     ],
                    //     [
                    //         'text' => 'Excel 匯入',
                    //         'path' => '/courses-import?from=excel',
                    //     ]

                    // )
                ],
                [
                    'id' => 4,
                    'text' => '匯入上課時間',
                    'path' => '/classtimes-import',
                    'active' => $current=='classtimes-import',
                    // 'items' => array(
                    //     [
                    //         'text' => '從舊課程複製',
                    //         'path' => '/courses-import',
                    //     ],
                    //     [
                    //         'text' => 'Excel 匯入',
                    //         'path' => '/courses-import?from=excel',
                    //     ]

                    // )
                ],
                // [
                //     'id' => 4,
                //     'text' => '新增課程',
                //     'path' => '/courses/create',
                //     'active' => $current=='courses/create'
                // ],
                // [
                //     'id' => 4,
                //     'text' => '狀態查詢',
                //     'path' => '/statuses',
                //     'active' => $current=='statuses'
                // ],
                
            );
        }
        if($key =='credit_courses')
        {
           return array(
                [
                    'id' => 1,
                    'text' => '學分班課程管理',
                    'path' => '/credit-courses',
                    'active' => $current=='credit-courses'
                ],
                [
                    'id' => 2,
                    'text' => '匯入學分班課程',
                    'path' => '/credit-courses-import',
                    'active' => $current=='credit-courses-import',
                    
                ],
                // [
                //     'id' => 4,
                //     'text' => '新增課程',
                //     'path' => '/courses/create',
                //     'active' => $current=='courses/create'
                // ],
                // [
                //     'id' => 4,
                //     'text' => '狀態查詢',
                //     'path' => '/statuses',
                //     'active' => $current=='statuses'
                // ],
                
            );
        }
        if($key =='students'|| $key =='admissions'|| $key =='registers' 
            || $key =='lessons' || $key =='scores'){
            return array(
            
                [
                    'id' => 1,
                    'text' => '錄取名單',
                    'path' => '/admissions',
                    'active' => $current=='admissions'
                ],
                [
                    'id' => 2,
                    'text' => '註冊學員名單',
                    'path' => '/course-registers',
                    'active' => $current=='course-registers'
                ],
                [
                    'id' => 3,
                    'text' => '課堂紀錄表',
                    'path' => '/lessons',
                    'active' => $current=='lessons'
                ],
                [
                    'id' => 4,
                    'text' => '學員成績',
                    'path' => '/scores',
                    'active' => $current=='scores'
                ],
                
            );
        }
        if($key=='main_settings'){
            
            return array(
                [
                    'id' => 1,
                    'text' => '開課中心',
                    'path' => '/centers',
                    'active' => $current=='centers' ,  
                     
                ],[
                    'id' => 2,
                    'text' => '匯入開課中心',
                    'path' => '/centers-import',
                    'hide' => !\App\Center::canImport($current_user),
                    'active' => $current=='centers-import',  
                     
                ],[
                    'id' => 3,
                    'text' => '課程分類',
                    'path' => '/categories',
                    'active' => $current=='categories' ,                
                ],[
                    'id' => 4,
                    'text' => '匯入課程分類',
                    'path' => '/categories-import',
                    'hide' => !\App\Category::canImport($current_user),
                    'active' =>$current=='categories-import',                
                ],[
                    'id' => 5,
                    'text' => '學期設定',
                    'path' => '/terms',
                    'active' => $current=='terms'
                ],[
                    'id' => 6,
                    'text' => '文件下載管理',
                    'path' => '/downloads',
                    'active' => $current=='downloads'
                ],
                 
             );
        }
        if($key=='settings'){
           return array(
               
                [
                    'id' => 1,
                    'text' => '假日設定',
                    'path' => '/holidays',
                    'active' => $current=='holidays'
                ],
                
                [
                    'id' => 2,
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

        if($key=='admins'){
           return array(
                [
                    'id' => 1,
                    'text' => '權限管理',
                    'path' => '/admins',
                    'active' => $current=='admins'
                ],
                [
                    'id' => 2,
                    'text' => '新增管理員',
                    'path' => '/admins/create',
                    'active' => $current=='admins/create'
                ],
                [
                    'id' => 3,
                    'text' => '匯入管理員',
                    'path' => '/admins-import',
                    'active' => $current=='admins-import'
                ],
                
                
            );
        }

        if($key=='notices'){
           return array(
                [
                    'id' => 1,
                    'text' => '公告管理',
                    'path' => '/notices',
                    'active' => $current=='notices'
                ],
                [
                    'id' => 2,
                    'text' => '新增公告',
                    'path' => '/notices/create',
                    'active' => $current=='notices/create'
                ],
                
                
            );
        }

        if($key=='reports'){
           return array(
                [
                    'id' => 1,
                    'text' => '課程清單',
                    'path' => '/courses-report',
                    'active' => $current=='courses-report'
                ],
               
                
                
            );
        }

        return [];
    }
}
