<?php

use Illuminate\Http\Request;
use App\posts;

// Route::group([
//     'prefix' => 'auth'
// ], function () {
//     Route::post('login', 'Auth\AuthController@login')->name('login');
//     Route::post('register', 'Auth\AuthController@register');
//     Route::group([
//       'middleware' => 'auth:api'
//     ], function() {
//         Route::get('logout', 'Auth\AuthController@logout');
//         Route::get('user', 'Auth\AuthController@user');
//     });
// });

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth:api')->get('/admin', function (Request $request) {
    return $request->admin();
});
Route::middleware('auth:api')->get('/hello', function (Request $request) {
    return 'hello';
});
Route::post('login', 'APIUserController@login')->middleware('cors');
Route::post('login/avatar', 'APIUserController@updateAvatar')->middleware('cors');
Route::post('/admin/login', 'AdminController@login')->middleware('cors');
Route::post('register', 'APIUserController@register')->middleware('cors');
Route::delete('user/delete/{id}', 'APIUserController@deleteUser')->middleware('cors');
Route::put('user/update/{id}', 'APIUserController@updateUser')->middleware('cors');
Route::middleware('auth:api')->get('/details','APIUserController@details')->middleware('cors');

//FACEBOOK LOGIN
Route::get('/redirect', 'SocialAuthFacebookController@redirect');
Route::get('/callback', 'SocialAuthFacebookController@callback');


//------------------------------CRUD----------------------------
//GET
Route::get('/data',function(){
	$items = posts::all()->toJson();
	return response($items,200)->header('Content-Type', 'application/json');
});

//GET POST by ID
Route::get('/data/{id}', 'PostController@myPost')->middleware('cors');

//CREATE
Route::post('/data/create', 'PostController@createPost')->middleware('cors');

//UPDATE
Route::put('/data/update/{id}', 'PostController@updatePost')->middleware('cors');

//DELETE
Route::delete('/data/delete/{id}', 'PostController@deletePost')->middleware('cors');
