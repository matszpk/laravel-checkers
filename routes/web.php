<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function() {
    return view('welcome');
})->name('home');

Auth::routes(['verify' => true]);
Route::get('/logout', 'Auth\LoginController@logout');

Route::get('/users', 'UserController@index')->name('user.list');

Route::get('/user/{userId}', 'UserController@getUser');
Route::post('/user/{userId}', 'UserController@addComment');

Route::get('/user/{userId}/edit', 'UserController@editUser');
Route::post('/user/{userId}/edit', 'UserController@updateUser')->name('userUpdate');

Route::post('/user/{userId}/like', 'UserController@likeUser');

Route::get('/user/{userId}/wcomments', 'UserController@writtenComments');
