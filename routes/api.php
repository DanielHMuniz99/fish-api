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

 Route::resource('users', 'UserController');

//  Route::resource('fish', 'FishController');

//  Route::get('/fish', 'FishController@index')->name('fish');


 Route::get('/fish', 'App\Http\Controllers\FishController@index');
 Route::get('/fish/{id}', 'App\Http\Controllers\FishController@show');