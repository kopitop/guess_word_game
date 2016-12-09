<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', 'HomeController@index');

Auth::routes();

Route::get('/home', 'HomeController@index');

//User features
Route::group(['namespace' => 'Web', 'middleware' => 'auth'], function () {
    Route::get('users/change-password', 'UsersController@editPassword');
    Route::put('users/change-password', 'UsersController@updatePassword');
    Route::resource('users', 'UsersController', ['only'=> [
        'show', 'update', 'edit',
    ]]);

    Route::get('rooms/join/{id}', 'RoomsController@join');
    Route::post('rooms/quit', 'RoomsController@quit');
	Route::post('rooms/refresh', 'RoomsController@refresh');
	Route::post('rooms/ready', 'RoomsController@updateReadyState');
	Route::post('rooms/start-to-play', 'RoomsController@beginPlay');
    Route::resource('rooms', 'RoomsController', ['only' => [
	    'index', 'store', 'update', 'show'
    ]]);
});
