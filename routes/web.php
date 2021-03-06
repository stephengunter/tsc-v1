<?php
Route::get('/test', function(){
    
    return view('test');
    
});

Route::post('/test-import-schedules', function(Request $form){
    $form=request();
    if(!$form->hasFile('schedules_file')){
        
         return   response()
                     ->json(['schedules_file' => ['無法取得上傳檔案'] 
                         ]  ,  422);      
    }

    $current_user=auth()->user();
     $file=$form->file('schedules_file'); 

    $schedules=new \App\Repositories\Schedules();
    

     $err_msg=$schedules->importAllSchedules($file,$current_user);
     

     if($err_msg) {
          return response()->json(['error' => $err_msg,'code' => 422 ], 422);
      
     }

    

     return response()->json(['success' => true]);
    
});
Route::post('/test-import-classtimes', function(){
    $form=request();
    if(!$form->hasFile('classtimes_file')){
        
         return   response()
                     ->json(['classtimes_file' => ['無法取得上傳檔案'] 
                         ]  ,  422);      
    }

   

    $current_user=auth()->user();
     $file=$form->file('classtimes_file'); 

    $classtimes=new \App\Repositories\Classtimes();
    

     $err_msg=$classtimes->importAll($file,$current_user);
     

     if($err_msg) {
          return response()->json(['error' => $err_msg,'code' => 422 ], 422);
      
     }

    

     return response()->json(['success' => true]);
     
 });
//  Route::get('/test', function(){
//    //  Artisan::call('migrate');
//   Artisan::call('db:seed');
    
//  });


// Route::get('admins/index-options', function(){
//    dd('dd');
    
// });

Route::get('errors', function(){
     return view('errors');
});

//Auth
Route::get('login', '\App\Http\Controllers\Auth\SessionsController@create')->name('login');
Route::post('login', '\App\Http\Controllers\Auth\SessionsController@store');
Route::post('logout', '\App\Http\Controllers\Auth\SessionsController@destroy');

Route::get('email-unconfirmed/{email}', '\App\Http\Controllers\Auth\EmailConfirmController@create')->name('email-unconfirmed');
Route::post('send-confirmation-mail', '\App\Http\Controllers\Auth\EmailConfirmController@sendMail')->name('send-confirmation-mail');
Route::get('email-confirmation/user/{user}/token/{token}','\App\Http\Controllers\Auth\EmailConfirmController@confirmEmail');

Route::get('reset-password/user/{user}/token/{token}','\App\Http\Controllers\Auth\ResetPasswordController@create');


Route::group(['middleware' => 'admin'], function()
{
    
    Route::get('/', 'HomeController@index');

    Route::post('downloads/display-order',['uses'=>'DownloadsController@updateDisplayOrder']);
    Route::resource('downloads', 'DownloadsController',['except'=>['update']]);

    Route::resource('photoes', 'PhotoesController',['only'=>['show','store']]);

    //User
    
    Route::put('users/{user}/update-contactinfo',['uses'=>'\App\Http\Controllers\User\UsersController@updateContactInfo']);
    Route::put('users/{user}/update-photo',['uses'=>'\App\Http\Controllers\User\UsersController@updatePhoto']);
    Route::post('users/find-users', ['uses' => '\App\Http\Controllers\User\UsersController@findUsers']);

    Route::resource('volunteers', '\App\Http\Controllers\User\VolunteersController');
    Route::resource('volunteers-import', '\App\Http\Controllers\User\VolunteersImportController',
    ['only' => ['index','store']]);

    //Center
    Route::get('centers/options', '\App\Http\Controllers\Settings\CentersController@options');
    Route::post('centers/display-order','\App\Http\Controllers\Settings\CentersController@updateDisplayOrder');
    Route::put('centers/{id}/update-photo','\App\Http\Controllers\Settings\CentersController@updatePhoto');
    Route::resource('centers', '\App\Http\Controllers\Settings\CentersController');

    Route::resource('centers-import', '\App\Http\Controllers\Settings\CentersImportController',
    ['only' => ['index','store']]);

    //Category
    Route::post('categories/display-order',['uses'=>'\App\Http\Controllers\Course\CategoriesController@updateDisplayOrder']);
    Route::resource('categories', '\App\Http\Controllers\Course\CategoriesController'); 
    
    Route::resource('categories-import', '\App\Http\Controllers\Course\CategoriesImportController',
    ['only' => ['index','store']]);
    

    //Teacher
    Route::get('teachers/options', '\App\Http\Controllers\Teacher\TeachersController@options');
    Route::resource('teachers', '\App\Http\Controllers\Teacher\TeachersController');

    Route::put('group-teachers/{id}/remove',['uses'=>'\App\Http\Controllers\Teacher\GroupTeachersController@remove']);   
    Route::resource('group-teachers', '\App\Http\Controllers\Teacher\GroupTeachersController',
    ['only' => ['index','create','store']]);

    Route::resource('teachers-import', '\App\Http\Controllers\Teacher\TeachersImportController',
    ['only' => ['index','store']]);

    
    
    //Course
    Route::get('courses/search', '\App\Http\Controllers\Course\CoursesController@search');
    Route::get('courses/sub-courses', '\App\Http\Controllers\Course\CoursesController@subCourses');
    Route::get('courses/options', '\App\Http\Controllers\Course\CoursesController@options');
    Route::get('courses/group-options', '\App\Http\Controllers\Course\CoursesController@groupOptions');
    Route::get('courses/index-options', '\App\Http\Controllers\Course\CoursesController@indexOptions');
    Route::post('courses/update-numbers','\App\Http\Controllers\Course\CoursesController@updateNumbers');

    
    Route::put('courses/{id}/update-photo',['uses'=>'\App\Http\Controllers\Course\CoursesController@updatePhoto']);
    Route::resource('courses', '\App\Http\Controllers\Course\CoursesController');

    //Route::post('courses-import/copy', '\App\Http\Controllers\Course\CoursesImportController@copy');
    Route::resource('courses-import', '\App\Http\Controllers\Course\CoursesImportController',
    ['only' => ['index','store']]);

    Route::resource('credit-courses-import', '\App\Http\Controllers\Course\Credit\ImportsController',
    ['only' => ['index','store']]);
   
    Route::resource('course-signup-infoes', '\App\Http\Controllers\Course\SignupInfoesController',
    ['only' => ['show','edit','update']]);

    Route::resource('classtimes-import', '\App\Http\Controllers\Course\ClasstimesImportController',
    ['only' => ['index','store']]);

    Route::resource('classtimes', '\App\Http\Controllers\Course\ClassTimesController');

    Route::resource('courses-review', '\App\Http\Controllers\Course\CoursesReviewController',
    ['only' => ['index','store','update']]);

    Route::resource('schedules', '\App\Http\Controllers\Course\SchedulesController');

    Route::post('schedules-import/excel', '\App\Http\Controllers\Course\SchedulesImportController@excelImport');
    Route::resource('schedules-import', '\App\Http\Controllers\Course\SchedulesImportController', 
    ['only' => ['create','store']]);


    //Signups
    
    Route::post('discounts/display-order',['uses'=>'\App\Http\Controllers\Signups\DiscountsController@updateDisplayOrder']);
    Route::get('discounts/options', '\App\Http\Controllers\Signups\DiscountsController@options');
    Route::get('discounts/count-tuition', '\App\Http\Controllers\Signups\DiscountsController@countTuition');
    Route::resource('discounts', '\App\Http\Controllers\Signups\DiscountsController');

    Route::get('signups/index-options', '\App\Http\Controllers\Signups\SignupsController@indexOptions');
    Route::get('signups/status-options', '\App\Http\Controllers\Signups\SignupsController@statusOptions');
    
    
    Route::post('signups/update-user', '\App\Http\Controllers\Signups\SignupsController@updateUser');

    Route::get('signups/new-user', '\App\Http\Controllers\Signups\NewUserSignupsController@create');
    Route::post('signups/new-user', '\App\Http\Controllers\Signups\NewUserSignupsController@store');

  
    Route::resource('signups', '\App\Http\Controllers\Signups\SignupsController');

    Route::get('bills/discount-options', '\App\Http\Controllers\Signups\BillsController@discountOptions');
    Route::get('bills/{id}/print', '\App\Http\Controllers\Signups\BillsController@print');
    Route::resource('bills', '\App\Http\Controllers\Signups\BillsController');


    
    

    
    
    
    
    

    Route::get('refunds/status-options', '\App\Http\Controllers\Signups\RefundsController@statusOptions');
    Route::get('refunds/{id}/print', '\App\Http\Controllers\Signups\RefundsController@print');

   

   
    

    Route::get('lessons/{id}/print', '\App\Http\Controllers\Course\LessonsController@print');

    

    

    

    Route::post('scores/export', '\App\Http\Controllers\Students\ScoresController@export');
    Route::post('scores/import', '\App\Http\Controllers\Students\ScoresController@import');
    Route::post('files/upload', '\App\Http\Controllers\FilesController@upload');

    Route::get('notices-email', '\App\Http\Controllers\Notice\NoticesController@email');


    

    Route::resource('change-password', '\App\Http\Controllers\Auth\ChangePasswordController',['only' => ['index','store']]);
    Route::resource('forgot-password', '\App\Http\Controllers\Auth\ForgotPasswordController',['only' => ['index','store']]);
    Route::resource('reset-password', '\App\Http\Controllers\Auth\ResetPasswordController',['only' => ['store']]);



    Route::resource('titles', '\App\Http\Controllers\User\TitlesController');
    

   

   

    Route::resource('contactinfoes', '\App\Http\Controllers\Contact\ContactInfoesController');
    Route::resource('address', '\App\Http\Controllers\Contact\AddressController');
    Route::resource('cities', '\App\Http\Controllers\Contact\CitiesController', ['only' => ['index']]);
    Route::resource('districts', '\App\Http\Controllers\Contact\DistrictsController', ['only' => ['index']]);

    
    Route::resource('tuitions', '\App\Http\Controllers\Signups\TuitionsController');
    Route::resource('back-tuitions', '\App\Http\Controllers\Signups\BackTuitionsController');
    Route::resource('refunds', '\App\Http\Controllers\Signups\RefundsController');

    Route::resource('notices', '\App\Http\Controllers\Notice\NoticesController');

    
    Route::resource('statuses', '\App\Http\Controllers\Course\StatusesController',
                                    ['only' => ['index','show','edit','update']]);
    Route::resource('admissions', '\App\Http\Controllers\Course\AdmissionsController');  
    Route::resource('course-registers', '\App\Http\Controllers\Course\RegistersController');  
    Route::resource('students', '\App\Http\Controllers\Course\StudentsController');  
    Route::resource('scores', '\App\Http\Controllers\Students\ScoresController',
                                            ['only' => ['index','store']]);   

    
    
    
    Route::resource('lessons', '\App\Http\Controllers\Course\LessonsController');
    Route::resource('lessons-initialize', '\App\Http\Controllers\Course\LessonsInitializeController', 
                                        ['only' => ['create','store']]);   
    Route::resource('lesson-participants', '\App\Http\Controllers\Course\LessonParticipantsController',
                                        ['only' => ['index','update']]);   
    Route::resource('leaves', '\App\Http\Controllers\Course\LeavesController');

                                                                     
    
    
    
    Route::resource('category-courses', '\App\Http\Controllers\Course\CategoryCourseController');                                                                  


    

    
    Route::resource('identities', '\App\Http\Controllers\Discounts\IdentitiesController');

    Route::resource('terms', '\App\Http\Controllers\Settings\TermsController');
    
    Route::resource('holidays', '\App\Http\Controllers\Settings\HolidaysController');


    //Reports
    Route::resource('courses-report', '\App\Http\Controllers\Reports\CourseListController',
    ['only' => ['index','store']]); 

    Route::post('signups-report/import-stops', '\App\Http\Controllers\Reports\SignupsController@importStopCourses');
    Route::resource('signups-report', '\App\Http\Controllers\Reports\SignupsController',
    ['only' => ['index','store']]); 


    
    Route::resource('user-centers', '\App\Http\Controllers\User\UserCenterController');
    Route::resource('users', '\App\Http\Controllers\User\UsersController');
    Route::resource('user-roles', '\App\Http\Controllers\User\UserRolesController');
                                           
});

Route::group(['middleware' => 'owner'], function()
{
    Route::get('admins/index-options', '\App\Http\Controllers\Admin\AdminsController@indexOptions');
    Route::resource('admins', '\App\Http\Controllers\Admin\AdminsController'); 

    Route::resource('admins-import', '\App\Http\Controllers\Admin\AdminsImportController',['only' => ['index','store']]);
                                           

    Route::resource('teachers-review', '\App\Http\Controllers\Teacher\TeachersReviewController',
    ['only' => ['index','store','update']]);
});



