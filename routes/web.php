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

Route::prefix('/user')->group(function() {
    Route::get('{userId}', 'UserController@getUser')->name('user.user');
    Route::post('{userId}', 'UserController@addComment');
    
    Route::get('{userId}/edit', 'UserController@editUser')->name('user.edit');
    Route::post('{userId}/edit', 'UserController@updateUser')->name('user.update');
    
    Route::post('{userId}/like', 'UserController@likeUser')->name('user.like');
    
    Route::get('{userId}/wcomments', 'UserController@writtenComments')
            ->name('user.wcomments');
});

Route::post('/comment/{commentId}/like', 'CommentController@likeComment')
        ->name('comment.like');

Route::prefix('/games')->group(function() {
    Route::get('list/{userId?}', 'GameController@index')->name('game.list');
    Route::get('tocont', 'GameController@listGamesToContinue')
            ->name('game.listToContinue');
    Route::get('tojoin', 'GameController@listGamesToJoin')
            ->name('game.listToJoin');
    Route::get('toreplay', 'GameController@listGamesToReplay')
            ->name('game.listToReplay');
});

Route::prefix('/game')->group(function() {
    Route::get('newgame', 'GameController@newGame')->name('game.new');
    Route::get('create/{asPlayer1}', 'GameController@createGame')
            ->name('game.create');
    Route::get('{gameId}/join', 'GameController@joinToGame')
            ->name('game.join');
    
    Route::get('{gameId}/play', 'GameController@playGame')
            ->name('game.play');
    Route::get('{gameId}/replay', 'GameController@replayGame')
            ->name('game.replay');
    Route::get('{gameId}/move', 'GameController@makeMove')
            ->name('game.move');
    Route::get('{gameId}/state', 'GameController@getGameState')->name('game.state');
    
    Route::get('{gameId}/choose', 'GameController@chooseSide')
            ->name('game.chooseSide');
});

