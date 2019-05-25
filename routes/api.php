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
    Route::post('/save', 'ArticlesController@save')->middleware('auth:api');
    Route::post('/get-list', 'ArticlesController@getList');
    Route::get('/get-one/{id}', 'ArticlesController@getOne');
    Route::get('/remove/{id}', 'ArticlesController@remove')->middleware('auth:api');
});

Route::group([
    'prefix' => 'comments',
], function () {
    Route::post('/save', 'CommentsController@save')->middleware('auth:api');
    Route::post('/get-list', 'CommentsController@getList');
    Route::get('/remove/{id}', 'CommentsController@remove')->middleware('auth:api');
});

Route::group([
    'prefix' => 'favorites',
    'middleware'=> 'auth:api'
], function () {
    Route::get('/get-user-favorites', 'FavoritesController@getUserFavorites');
    Route::post('/toggle-favorite', 'FavoritesController@toggleFavorite');
});

Route::group([
    'prefix' => 'users',
], function () {
    Route::post('/regist', 'UsersController@regist');
    Route::post('/login', 'UsersController@login');
    Route::get('/accept-registration/{token}', 'UsersController@acceptRegistration');

    Route::group([
        'middleware' => ['auth:api'],
    ], function () {
        Route::get('/get-curr-user', 'UsersController@getCurrUser');
        Route::post('/is-another-password', 'UsersController@isAnotherPassword');
        Route::post('/update', 'UsersController@update');
        Route::post('/delete', 'UsersController@delete');
        Route::get('/logout', 'UsersController@logout');
    });
});

Route::group([
    'prefix' => 'main',
], function () {
    Route::post('/get-blogers-rating-list', 'MainController@getBlogersRatingList');
    Route::post('/get-most-popular-articles', 'MainController@getMostPopularArticles');
});

Route::get('/files/download-file/{file_id}', 'FilesController@downloadFile');

Route::group([
    'prefix' => 'backend',
    'middleware' => ['auth:api', 'is_admin'],
    'namespace' => 'Backend'
], function () {
    Route::group([
        'prefix' => 'users',
    ], function () {
        Route::post('/get-list', 'UsersController@getList');
        Route::get('/get-user/{id}', 'UsersController@getUser');
    });
});


