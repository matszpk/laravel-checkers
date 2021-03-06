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

Route::prefix('/user')->name('user.')->group(function() {
    Route::get('{userId}', 'UserController@getUser')->name('user');
    Route::post('{commentableId}', 'UserController@addComment')->name('addComment');
    
    Route::get('{userId}/edit', 'UserController@editUser')->name('edit');
    Route::post('{userId}/edit', 'UserController@updateUser')->name('update');
    
    Route::post('{likelableId}/like', 'UserController@likeObject')->name('like');
    
    Route::get('{userId}/wcomments', 'UserController@writtenComments')
            ->name('wcomments');
});

Route::post('/comment/{likelableId}/like', 'CommentController@likeObject')
        ->name('comment.like');

Route::prefix('/games')->name('game.')->group(function() {
    Route::get('list/{userId?}', 'GameController@index')->name('list');
    Route::get('tocont', 'GameController@listGamesToContinue')->name('listToContinue');
    Route::get('tojoin', 'GameController@listGamesToJoin')->name('listToJoin');
    Route::get('toreplay', 'GameController@listGamesToReplay')->name('listToReplay');
});

Route::prefix('/game')->name('game.')->group(function() {
    Route::get('newgame', 'GameController@newGame')->name('new');
    Route::get('create/{asPlayer1}', 'GameController@createGame')->name('create');
    Route::get('{gameId}/join', 'GameController@joinToGame')->name('join');
    
    Route::get('{gameId}/play', 'GameController@playGame')->name('play');
    Route::get('{gameId}/replay', 'GameController@replayGame')->name('replay');
    Route::post('{gameId}/move', 'GameController@makeMove')->name('move');
    Route::get('{gameId}/state', 'GameController@getGameState')->name('state');
    
    Route::get('{gameId}/choose', 'GameController@chooseSide')->name('chooseSide');
    
    Route::post('{likelableId}/like', 'GameController@likeObject')->name('like');
    
    Route::get('{gameId}/comments', 'GameController@getGameComments')->name('comments');
    Route::post('{commentableId}/comments', 'GameController@addComment')->name('addComment');
});

