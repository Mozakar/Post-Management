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

Route::group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers\Api\V1'], function () {

    Route::post('auth', 'AuthController@loginOrRegister');
    Route::post('auth/verify', 'AuthController@verify');


    /**
     * Admin
     */
    Route::group(['prefix' => 'panel', 'middleware' => ['auth:api', 'admin']], function (){

       /**
        * File Controller
        */
       route::get('files/list', 'FileController@list')->name('files.list');
       route::post('files/upload', 'FileController@upload')->name('files.upload');
       route::post('files/update', 'FileController@update')->name('files.update');
       route::get('files/{id}/delete', 'FileController@delete')->name('files.delete');


   });

});

