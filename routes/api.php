<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::post('register','Api\UserController@register');
Route::post('login','Api\UserController@login');

Route::group(['middleware'=>'auth:api'],function(){
    Route::get('logout','Api\UserController@logout');
    Route::get('user/{id}','Api\UserController@show');
    Route::put('user/{id}','Api\UserController@update');
    Route::put('userpass/{id}','Api\UserController@updatePassword');

});
