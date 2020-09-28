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

Route::get('/index', 'App\Http\Controllers\ApiController@index');
Route::get('/resetDemoData', 'App\Http\Controllers\ApiController@resetDemoData');

Route::post('/pay', 'App\Http\Controllers\ApiController@pay');
Route::post('/buy', 'App\Http\Controllers\ApiController@buy');
Route::get('/getChange', 'App\Http\Controllers\ApiController@getChange');
