<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/
Route::group(array('before' => 'auth'), function(){
    Route::get('/', 'HomeController@getindex');
});

Route::controller('user', 'UserController');
Route::get('user/edit/{id}', 'UserController@getEdit');
Route::post('user/edit/{id}', 'UserController@postEdit');
Route::get('user/chpwd/{id}', 'UserController@getChpwd');
Route::post('user/chpwd/{id}', 'UserController@postChpwd');
Route::get('user/delete/{id}', 'UserController@getDelete');
Route::post('user/delete/{id}', 'UserController@postDelete');

Route::group(array('before' => 'auth'), function(){
    Route::controller('admin', 'AdminController');
});