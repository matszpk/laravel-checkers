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
Route::get('/logout', 'Auth\LoginController@logout')->name('logout');

Route::get('/users', 'UserController@index')->name('user.list');

Route::get('/user/{userId}', 'UserController@getUser')->name('user.user');
Route::post('/user/{userId}', 'UserController@addComment');

Route::get('/user/{userId}/edit', 'UserController@editUser')->name('user.edit');
Route::post('/user/{userId}/edit', 'UserController@updateUser')->name('user.update');

Route::post('/user/{userId}/like', 'UserController@likeUser')->name('user.like');

Route::get('/user/{userId}/wcomments', 'UserController@writtenComments')
        ->name('user.wcomments');

Route::post('/comment/{commentId}/like', 'CommentController@likeComment')
        ->name('comment.like');

Route::get('/games/list/{userId?}', 'GameController@index')->name('game.list');
Route::get('/games/tocont', 'GameController@listGamesToContinue')
        ->name('game.listToContinue');
Route::get('/games/tojoin', 'GameController@listGamesToJoin')
        ->name('game.listToJoin');
Route::get('/games/toreplay', 'GameController@listGamesToReplay')
        ->name('game.listToReplay');

Route::get('/game/newgame', 'GameController@newGame')->name('game.new');
Route::get('/game/create/{asPlayer1}', 'GameController@createGame')
        ->name('game.create');
Route::get('/game/{gameId}/join', 'GameController@joinToGame')
        ->name('game.join');

Route::get('/game/{gameId}/play', 'GameController@playGame')
        ->name('game.play');
Route::get('/game/{gameId}/replay', 'GameController@replayGame')
        ->name('game.replay');
Route::get('/game/{gameId}/move', 'GameController@makeMove')
        ->name('game.move');
