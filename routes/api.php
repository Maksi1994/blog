<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    'prefix' => 'articles',
], function () {
    Route::post('/save', 'ArticlesController@save');
    Route::post('/get-list', 'ArticlesController@getList');
    Route::get('/remove/{id}', 'ArticlesController@remove');
});

Route::group([
    'prefix' => 'comments',
], function () {
    Route::post('/save', 'CommentsController@save');
    Route::post('/get-list', 'CommentsController@getList');
    Route::get('/remove/{id}', 'CommentsController@remove');
});

Route::group([
    'prefix' => 'users',
], function () {
    Route::post('/create', 'UsersController@create');
    Route::post('/login', 'UsersController@login');
    Route::get('/accept-registration/{token}', 'UsersController@acceptRegistration');

    Route::group([
      'middleware' => ['auth:api']
    ], function() {
      Route::get('/get-curr-user', 'UsersController@getCurrUser');
      Route::post('/update', 'UsersController@update');
      Route::post('/delete', 'UsersController@delete');
      Route::get('/logout', 'UsersController@logout');
    });
});

Route::get('/files/download-file/{file_id}', 'FilesController@downloadFile');
