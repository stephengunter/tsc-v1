<?php

//  Route::get('/test', function(){
//    //  Artisan::call('migrate');
//   Artisan::call('db:seed');
    
//  });
Route::get('test', function(){
     return view('test');
});
Route::get('/', 'HomeController@index');
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

Route::put('users/{user}/update-contactinfo',['uses'=>'\App\Http\Controllers\User\UsersController@updateContactInfo']);
Route::put('users/{user}/update-photo',['uses'=>'\App\Http\Controllers\User\UsersController@updatePhoto']);
Route::post('users/find-users', ['uses' => '\App\Http\Controllers\User\UsersController@findUsers']);

Route::get('signups/new-user', '\App\Http\Controllers\Signups\NewUserSignupsController@create');
Route::post('signups/new-user', '\App\Http\Controllers\Signups\NewUserSignupsController@store');
Route::get('signups/index-options', '\App\Http\Controllers\Signups\SignupsController@indexOptions');
Route::get('signups/status-options', '\App\Http\Controllers\Signups\SignupsController@statusOptions');
Route::get('signups/{id}/print', '\App\Http\Controllers\Signups\SignupsController@print');

Route::get('refunds/status-options', '\App\Http\Controllers\Signups\RefundsController@statusOptions');
Route::get('refunds/{id}/print', '\App\Http\Controllers\Signups\RefundsController@print');

Route::put('categories/{id}/update-order',['uses'=>'\App\Http\Controllers\Course\CategoriesController@updateDisplayOrder']);

Route::get('admins/index-options', 'AdminsController@indexOptions');


Route::get('courses/search', '\App\Http\Controllers\Course\CoursesController@search');
Route::get('courses/options', '\App\Http\Controllers\Course\CoursesController@options');
Route::get('courses/index-options', '\App\Http\Controllers\Course\CoursesController@indexOptions');

Route::post('courses/import', '\App\Http\Controllers\Course\CoursesController@import');
Route::put('courses/{id}/update-photo',['uses'=>'\App\Http\Controllers\Course\CoursesController@updatePhoto']);

Route::get('lessons/{id}/print', '\App\Http\Controllers\Course\LessonsController@print');

Route::get('teachers/options', '\App\Http\Controllers\Teacher\TeachersController@options');

Route::get('centers/options', '\App\Http\Controllers\Settings\CentersController@options');
Route::put('centers/{id}/display-order',['uses'=>'\App\Http\Controllers\Settings\CentersController@updateDisplayOrder']);
Route::put('centers/{id}/update-photo',['uses'=>'\App\Http\Controllers\Settings\CentersController@updatePhoto']);

Route::resource('home', 'HomeController');

Route::resource('change-password', '\App\Http\Controllers\Auth\ChangePasswordController',['only' => ['index','store']]);
Route::resource('forgot-password', '\App\Http\Controllers\Auth\ForgotPasswordController',['only' => ['index','store']]);
Route::resource('reset-password', '\App\Http\Controllers\Auth\ResetPasswordController',['only' => ['store']]);

Route::resource('users', '\App\Http\Controllers\User\UsersController');
Route::resource('user-roles', '\App\Http\Controllers\User\UserRolesController');
Route::resource('user-centers', '\App\Http\Controllers\User\UserCenterController');
Route::resource('photoes', 'PhotoesController');

Route::resource('volunteers', '\App\Http\Controllers\User\VolunteersController');

Route::resource('admins', 'AdminsController');

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

Route::resource('categories', '\App\Http\Controllers\Course\CategoriesController');                                                                  
Route::resource('category-courses', '\App\Http\Controllers\Course\CategoryCourseController');                                                                  


Route::resource('teachers', '\App\Http\Controllers\Teacher\TeachersController');

Route::resource('discounts', '\App\Http\Controllers\Discounts\DiscountsController');
Route::resource('identities', '\App\Http\Controllers\Discounts\IdentitiesController');

Route::resource('terms', '\App\Http\Controllers\Settings\TermsController');
Route::resource('centers', '\App\Http\Controllers\Settings\CentersController');
Route::resource('holidays', '\App\Http\Controllers\Settings\HolidaysController');
Route::resource('classrooms', '\App\Http\Controllers\Settings\ClassroomsController');
Route::resource('titles', '\App\Http\Controllers\Settings\TitlesController');


// Route::group(['prefix' => 'api/'], function() {
//     Route::resource('sessions', '\App\Http\Controllers\Auth\SessionsController');
//     Route::resource('home', 'HomeController');
//     Route::resource('users', 'UsersController');
//     Route::resource('user-roles', 'UserRolesController');
//     Route::resource('teachers', 'TeachersController');
//     Route::resource('photoes', 'PhotoesController');
//     Route::resource('address', 'AddressController');
//     Route::resource('contactinfo', 'ContactInfoController');
//     Route::resource('centers', 'CentersController');
//     Route::resource('categories', 'CategoriesController');
//     Route::resource('courses', 'CoursesController');
//     Route::resource('classtimes', 'ClassTimesController');
//     Route::resource('schedules', 'SchedulesController');
//     Route::resource('terms', 'TermsController');
//     Route::resource('classrooms', 'ClassroomsController');
//     Route::resource('lessons', 'LessonsController');
//     Route::resource('titles', 'TitlesController');
//     Route::resource('volunteers', 'VolunteersController'); 
//     Route::resource('admins', 'AdminsController');   
//     Route::resource('holidays', 'HolidaysController');
//     Route::resource('centerteacher', 'CenterTeacherController');
//     Route::resource('centervolunteer', 'CenterVolunteerController');
//     Route::resource('centeradmin', 'CenterAdminController');
//     Route::resource('category-course', 'CategoryCourseController');
//     Route::resource('discounts', 'DiscountsController');
//     Route::resource('identities', 'IdentitiesController');
//     Route::resource('signups', 'SignupsController');
//     Route::resource('tuitions', 'TuitionsController');
//     Route::resource('back-tuitions', 'BackTuitionsController');
//     Route::resource('refunds', 'RefundsController');
// });



