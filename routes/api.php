<?php

use Illuminate\Http\Request;

Route::get('/user', function (Request $request) {
     $user=$request->user();
     $user->isTeacher=$user->isTeacher();
     $user->isStudent=$user->isStudent();
     return $user;
})->middleware('auth:api');

Route::get('/centers', ['uses' => '\App\Http\Controllers\Api\CentersController@index']);
Route::get('/categories', ['uses' => '\App\Http\Controllers\Api\CategoriesController@index']);
Route::get('/courses', ['uses' => '\App\Http\Controllers\Api\CoursesController@index']);
Route::get('/courses/{id}', ['uses' => '\App\Http\Controllers\Api\CoursesController@show']);


Route::post('/register',['uses'=>'\App\Http\Controllers\Api\RegisterController@store']);
Route::post('/email-confirmation',['uses'=>'\App\Http\Controllers\Api\RegisterController@confirmEmail']);
Route::post('/send-confirmation-mail',['uses'=>'\App\Http\Controllers\Api\RegisterController@sendConfirmationMail']);



// Route::post('/password/forgot', array('uses' => '\App\Http\Controllers\Auth\ResetPasswordController@forgot'));
// Route::post('/password/reset',['uses'=>'\App\Http\Controllers\Auth\ResetPasswordController@reset']);
// Route::post('/password/change',['uses'=>'PasswordController@change']);


// Route::get('/centers/activeCenters', ['uses' => 'CentersController@activeCenters']);
// Route::get('/categories/activeCategories', ['uses' => 'CategoriesController@activeCategories']);
// Route::get('/courses/activeCourses', ['uses' => 'CoursesController@activeCourses']);
// Route::get('/courses/details/{id}', ['uses' => 'CoursesController@details']);
// Route::get('/discounts/active-discounts', ['uses' => 'DiscountsController@activeDiscounts']);

// Route::post('/register',['uses'=>'RegisterController@register']);
// Route::post('/email-confirmation', array('as' => 'email-confirmation', 'uses' => 'RegisterController@confirmEmail'));
// Route::post('/send-confirmation-mail', array('uses' => 'RegisterController@sendConfirmationMail'));


// Route::get('/teachers/{user}/create', ['uses' => 'TeachersController@create']);
// Route::get('/users/{user}/roles', ['uses' => 'UsersController@roles']);
// Route::get('/users/{user}/rolesCanAdd', ['uses' => 'UsersController@rolesCanAdd']);
// Route::get('/users/userWithCenters/{id}', ['uses' => 'UsersController@userWithCenters']);

// Route::put('/teachers/{user}/update-user',['uses'=>'TeachersController@updateUser']);
// Route::put('/volunteers/{user}/update-user',['uses'=>'VolunteersController@updateUser']);
// Route::put('/admins/{user}/update-user',['uses'=>'AdminsController@updateUser']);

// Route::post('/users/addToRole',['uses'=>'UsersController@addToRole']);
// Route::post('/users/removeFromRole',['uses'=>'UsersController@removeFromRole']);
// Route::put('/users/{user}/updateContactInfo',['uses'=>'UsersController@updateContactInfo']);

// Route::get('/categories/options',['uses'=>'CategoriesController@options']);
// Route::get('/categories/allOptions',['uses'=>'CategoriesController@allOptions']);



// Route::put('/users/{user}/updatePhoto',['uses'=>'UsersController@updatePhoto']);

// Route::get('/centers/options',['uses'=>'CentersController@options']);
// Route::get('/centers/adminCenterOptions',['uses'=>'CentersController@adminCenterOptions']);
// Route::put('/centers/{center}/updatePhoto',['uses'=>'CentersController@updatePhoto']);
// Route::put('/centers/{center}/updateContactInfo',['uses'=>'CentersController@updateContactInfo']);

// Route::put('/courses/{course}/updatePhoto',['uses'=>'CoursesController@updatePhoto']);

// Route::get('/courses/{course}/showSignup',['uses'=>'CoursesController@showSignup']);
// Route::get('/courses/indexOptions',['uses'=>'CoursesController@indexOptions']);
// Route::get('/courses/{course}/editSignup',['uses'=>'CoursesController@editSignup']);
// Route::get('/courses/optionsByTeacher/{teacher}',['uses'=>'CoursesController@optionsByTeacher']);
// Route::get('/courses/options',['uses'=>'CoursesController@options']);
// Route::put('/courses/{course}/updateSignup',['uses'=>'CoursesController@updateSignup']);

// Route::get('/teachers/optionsByCourse/{course}',['uses'=>'TeachersController@optionsByCourse']);
// Route::get('/teachers/optionsByCenter/{center}',['uses'=>'TeachersController@optionsByCenter']);


// Route::put('/centers/{center}/displayOrder',['uses'=>'CentersController@updateDisplayOrder']);
// Route::put('/categories/{category}/displayOrder',['uses'=>'CategoriesController@updateDisplayOrder']);


// Route::get('/photoes/defaultProfile',['uses'=>'PhotoesController@defaultProfile']);
// Route::get('/photoes/defaultCenter',['uses'=>'PhotoesController@defaultCenter']);
// Route::get('/photoes/defaultCourse',['uses'=>'PhotoesController@defaultCourse']);

// Route::get('/cities',['uses'=>'AddressController@cities']);
// Route::get('/{city}/districts',['uses'=>'AddressController@districts']);


// Route::post('/schedules/import',['uses'=>'SchedulesController@import']);

// Route::get('/lessons/initialize/{course}',['uses'=>'LessonsController@initializeForm']);
// Route::post('/lessons/initialize',['uses'=>'LessonsController@initialize']);
// Route::post('/lessons/dayOff',['uses'=>'LessonsController@dayOff']);


// Route::get('/classrooms/options/{center}',['uses'=>'ClassroomsController@options']);

// Route::post('/centerteacher/remove',['uses'=>'CenterTeacherController@remove']);
// Route::post('/centervolunteer/remove',['uses'=>'CenterVolunteerController@remove']);
// Route::post('/centeradmin/remove',['uses'=>'CenterAdminController@remove']);

// Route::get('/terms/options',['uses'=>'TermsController@options']);

// Route::get('/admins/indexOptions',['uses'=>'AdminsController@indexOptions']);



// Route::get('/category-course/index-options',['uses'=>'CategoryCourseController@indexOptions']);
// Route::get('/category-course/courses-not-in-Category',['uses'=>'CategoryCourseController@courseNotInCategory']);
// Route::post('/category-course/import',['uses'=>'CategoryCourseController@import']);
// Route::post('/category-course/remove',['uses'=>'CategoryCourseController@remove']);


// Route::get('/identities/options',['uses'=>'IdentitiesController@options']);

// Route::get('/signups/getByUser/{user}',['uses'=>'SignupsController@getByUser']);
// Route::get('/signups/statusOptions',['uses'=>'SignupsController@statusOptions']);
// Route::get('/signups/indexOptions',['uses'=>'SignupsController@indexOptions']);

// Route::get('/refunds/indexOptions',['uses'=>'RefundsController@indexOptions']);


// Route::get('/accounts/getByUser/{user}',['uses'=>'AccountsController@getByUser']);


// Route::group(['middleware' => 'auth:api'], function(){
//      Route::resource('users', 'UsersController');
// });