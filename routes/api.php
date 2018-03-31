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
//api中间件，有session
Route::group(['middleware' => ['api']], function () {
    //工具路由
    Route::get('/return', ['uses' => 'Home\ReturnController@ReturnStandard'])->name('return');

    //普通用户接口
    Route::group(['prefix' => 'home'], function () {
        Route::post('showSimplePushes', ['uses' => 'Home\IndexController@showSimplePushes']);
        Route::post('showDetailPush', ['uses' => 'Home\IndexController@showDetailPush']);
        Route::post('showHomePage', ['uses' => 'Home\IndexController@showHomePage']);
    });
    //admin接口
    Route::group(['prefix' => 'admin'], function () {
        //用户
        Route::post('login', ['uses' => 'Admin\UserController@login']);
        Route::post('logOut', ['uses' => 'Admin\UserController@logOut']);
        //推送
        Route::post('uploadImageForUrl', ['uses' => 'Admin\PushController@uploadImageForUrl']);
        Route::post('uploadImagesForUrls', ['uses' => 'Admin\PushController@uploadImagesForUrls']);
        Route::post('addPush', ['uses' => 'Admin\PushController@addPush']);
        Route::post('updatePush', ['uses' => 'Admin\PushController@updatePush']);
        Route::post('deletePush', ['uses' => 'Admin\PushController@deletePush']);
        Route::post('showSimplePushes', ['uses' => 'Admin\PushController@showSimplePushes']);
        Route::post('showDetailPush', ['uses' => 'Admin\PushController@showDetailPush']);
        Route::post('searchPush', ['uses' => 'Admin\PushController@searchPush']);
    });
});

