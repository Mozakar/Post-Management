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
       route::get('files/list', 'FileController@list')->name('files.v1_list');
       route::post('files/upload', 'FileController@upload')->name('files.v1_upload');
       route::post('files/update', 'FileController@update')->name('files.v1_update');
       route::get('files/{id}/delete', 'FileController@delete')->name('files.v1_delete');


        /**
         * Post
         */
        route::get('posts', 'PostController@get')->name('posts.v1_index');
        route::get('posts/{id}/show', 'PostController@show')->name('posts.v1_show');
        route::post('posts/create', 'PostController@create')->name('posts.v1_create');
        route::post('posts/update', 'PostController@update')->name('posts.v1_update');
        route::post('posts/update-status', 'PostController@updateStatus')->name('posts.v1_update_status');
        route::get('posts/{id}/delete', 'PostController@delete')->name('posts.v1_delete');

   });

});

