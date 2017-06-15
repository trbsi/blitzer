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

Route::get('ajax-test', 'Controller@ajaxTest');
Route::get('/', 'Controller@index');
Route::get('legal', 'Controller@legal');

Route::group(['namespace' => 'Cron', 'prefix' => 'cron'], function () {
    Route::get('update-pin-time', 'PinController@updatePinTime');
    Route::get('clear-push-tokens', 'CronController@clearOldPushTokens');
});
