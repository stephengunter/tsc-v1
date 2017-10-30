<?php

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
    Route::get('/downloads', 'DownloadsController@index');

    Route::resource('photoes', 'PhotoesController',['only'=>['show','store']]);

    Route::get('centers/options', '\App\Http\Controllers\Settings\CentersController@options');
    Route::post('centers/display-order','\App\Http\Controllers\Settings\CentersController@updateDisplayOrder');
    Route::put('centers/{id}/update-photo','\App\Http\Controllers\Settings\CentersController@updatePhoto');
    Route::resource('centers', '\App\Http\Controllers\Settings\CentersController');

    Route::resource('centers-import', '\App\Http\Controllers\Settings\CentersImportController',['only' => ['index','store']]);


    Route::post('categories/display-order',['uses'=>'\App\Http\Controllers\Course\CategoriesController@updateDisplayOrder']);
    Route::resource('categories', '\App\Http\Controllers\Course\CategoriesController'); 
    
    Route::resource('categories-import', '\App\Http\Controllers\Course\CategoriesImportController',['only' => ['index','store']]);
    

    
    Route::get('teachers/options', '\App\Http\Controllers\Teacher\TeachersController@options');
    Route::resource('teachers', '\App\Http\Controllers\Teacher\TeachersController');

    Route::put('group-teachers/{id}/remove',['uses'=>'\App\Http\Controllers\Teacher\GroupTeachersController@remove']);   
    Route::resource('group-teachers', '\App\Http\Controllers\Teacher\GroupTeachersController',['only' => ['create','store','show']]);

    Route::resource('teachers-import', '\App\Http\Controllers\Teacher\TeachersImportController',['only' => ['index','store']]);

    Route::resource('teachers-review', '\App\Http\Controllers\Teacher\TeachersReviewController',['only' => ['index','store','update']]);
    

    Route::put('users/{user}/update-contactinfo',['uses'=>'\App\Http\Controllers\User\UsersController@updateContactInfo']);
    Route::put('users/{user}/update-photo',['uses'=>'\App\Http\Controllers\User\UsersController@updatePhoto']);
    Route::post('users/find-users', ['uses' => '\App\Http\Controllers\User\UsersController@findUsers']);

    Route::get('signups/new-user', '\App\Http\Controllers\Signups\NewUserSignupsController@create');
    Route::post('signups/new-user', '\App\Http\Controllers\Signups\NewUserSignupsController@store');
    Route::post('signups/update-user', '\App\Http\Controllers\Signups\SignupsController@updateUser');
    Route::get('signups/index-options', '\App\Http\Controllers\Signups\SignupsController@indexOptions');
    Route::get('signups/status-options', '\App\Http\Controllers\Signups\SignupsController@statusOptions');
    Route::get('signups/{id}/print', '\App\Http\Controllers\Signups\SignupsController@print');

    Route::get('refunds/status-options', '\App\Http\Controllers\Signups\RefundsController@statusOptions');
    Route::get('refunds/{id}/print', '\App\Http\Controllers\Signups\RefundsController@print');

   

   
    Route::get('courses/search', '\App\Http\Controllers\Course\CoursesController@search');
    Route::get('courses/sub-courses', '\App\Http\Controllers\Course\CoursesController@subCourses');
    Route::get('courses/options', '\App\Http\Controllers\Course\CoursesController@options');
    Route::get('courses/group-options', '\App\Http\Controllers\Course\CoursesController@groupOptions');
    Route::get('courses/index-options', '\App\Http\Controllers\Course\CoursesController@indexOptions');


    Route::post('courses/import', '\App\Http\Controllers\Course\CoursesController@import');
    Route::put('courses/{id}/update-photo',['uses'=>'\App\Http\Controllers\Course\CoursesController@updatePhoto']);

    Route::get('lessons/{id}/print', '\App\Http\Controllers\Course\LessonsController@print');

    

    

    

    Route::post('scores/export', '\App\Http\Controllers\Students\ScoresController@export');
    Route::post('scores/import', '\App\Http\Controllers\Students\ScoresController@import');
    Route::post('files/upload', '\App\Http\Controllers\FilesController@upload');

    Route::get('notices-email', '\App\Http\Controllers\Notice\NoticesController@email');


    

    Route::resource('change-password', '\App\Http\Controllers\Auth\ChangePasswordController',['only' => ['index','store']]);
    Route::resource('forgot-password', '\App\Http\Controllers\Auth\ForgotPasswordController',['only' => ['index','store']]);
    Route::resource('reset-password', '\App\Http\Controllers\Auth\ResetPasswordController',['only' => ['store']]);



    Route::resource('titles', '\App\Http\Controllers\User\TitlesController');
    

    Route::resource('volunteers', '\App\Http\Controllers\User\VolunteersController');

   

    Route::resource('contactinfoes', '\App\Http\Controllers\Contact\ContactInfoesController');
    Route::resource('address', '\App\Http\Controllers\Contact\AddressController');
    Route::resource('cities', '\App\Http\Controllers\Contact\CitiesController', ['only' => ['index']]);
    Route::resource('districts', '\App\Http\Controllers\Contact\DistrictsController', ['only' => ['index']]);

    Route::resource('signups', '\App\Http\Controllers\Signups\SignupsController');
    Route::resource('tuitions', '\App\Http\Controllers\Signups\TuitionsController');
    Route::resource('back-tuitions', '\App\Http\Controllers\Signups\BackTuitionsController');
    Route::resource('refunds', '\App\Http\Controllers\Signups\RefundsController');

    Route::resource('notices', '\App\Http\Controllers\Notice\NoticesController');

    Route::resource('courses', '\App\Http\Controllers\Course\CoursesController');
    Route::resource('statuses', '\App\Http\Controllers\Course\StatusesController',
                                    ['only' => ['index','show','edit','update']]);
    Route::resource('admissions', '\App\Http\Controllers\Course\AdmissionsController');  
    Route::resource('course-registers', '\App\Http\Controllers\Course\RegistersController');  
    Route::resource('students', '\App\Http\Controllers\Course\StudentsController');  
    Route::resource('scores', '\App\Http\Controllers\Students\ScoresController',
                                            ['only' => ['index','store']]);   

    Route::resource('course-signup-infoes', '\App\Http\Controllers\Course\SignupInfoesController',
                                            ['only' => ['show','edit','update']]);
    Route::resource('classtimes', '\App\Http\Controllers\Course\ClassTimesController');
    Route::resource('schedules', '\App\Http\Controllers\Course\SchedulesController');
    Route::resource('import-schedules', '\App\Http\Controllers\Course\ImportSchedulesController', 
                                        ['only' => ['create','store']]);
    Route::resource('lessons', '\App\Http\Controllers\Course\LessonsController');
    Route::resource('lessons-initialize', '\App\Http\Controllers\Course\LessonsInitializeController', 
                                        ['only' => ['create','store']]);   
    Route::resource('lesson-participants', '\App\Http\Controllers\Course\LessonParticipantsController',
                                        ['only' => ['index','update']]);   
    Route::resource('leaves', '\App\Http\Controllers\Course\LeavesController');

                                                                     
    
    
    
    Route::resource('category-courses', '\App\Http\Controllers\Course\CategoryCourseController');                                                                  


    

    Route::resource('discounts', '\App\Http\Controllers\Discounts\DiscountsController');
    Route::resource('identities', '\App\Http\Controllers\Discounts\IdentitiesController');

    Route::resource('terms', '\App\Http\Controllers\Settings\TermsController');
    
    Route::resource('holidays', '\App\Http\Controllers\Settings\HolidaysController');
    Route::resource('classrooms', '\App\Http\Controllers\Settings\ClassroomsController');

    Route::resource('courses-report', '\App\Http\Controllers\Reports\CourseListController',
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
                                           
});



