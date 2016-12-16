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

    Route::get('rooms/join/{id}', 'RoomsController@join')->name('rooms.join');
    Route::post('rooms/refresh', 'RoomsController@refresh')->name('rooms.refresh');
    Route::post('rooms/ready', 'RoomsController@updateReadyState')->name('rooms.ready');
    Route::post('rooms/quit', 'RoomsController@quit')->name('rooms.quit');
    Route::post('rooms/post-image', 'RoomsController@postImage')->name('rooms.post-image');
    Route::resource('rooms', 'RoomsController', ['only' => [
        'index', 'store', 'show',
    ]]);
    Route::resource('users', 'UsersController', ['only'=> [
        'show', 'update', 'edit',
    ]]);
});

//Admin features
Route::group(['middleware' => 'admin', 'prefix' => 'admin', 'namespace' => 'Admin'], function () {
    Route::get('/', 'HomeController@index');
    Route::resource('users', 'UsersController');
    Route::resource('words', 'WordsController', ['except' => [
        'delete', 'edit',
    ]]);
});

//API for nodejs
Route::get('rooms/reset-state/{id}', 'Web\RoomsController@resetState');
Route::get('rooms/begin-play/{id}', 'Web\RoomsController@beginPlay');
