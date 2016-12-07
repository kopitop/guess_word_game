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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index');

//User features
Route::group(['namespace' => 'Web', 'middleware' => 'auth'], function () {
	Route::post('rooms/quit', 'RoomsController@quit');
	Route::post('rooms/refresh', 'RoomsController@refresh');
    Route::resource('rooms', 'RoomsController', ['only' => [
        'index', 'store', 'update', 'show'
    ]]);
});
