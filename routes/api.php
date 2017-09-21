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


Route::get('/getProjectList', 'ProjectController@getProjectList');
Route::get('/getProjectInfo', 'ProjectController@getProjectInfo');

Route::get('/getSampleList','SampleController@getSampleList');
Route::get('/getSampleInfo','SampleController@getSampleInfo');

Route::get('/getRunList','RunController@getRunList');
Route::get('/getRunInfo','RunController@getRunInfo');

Route::get('/test', 'Test@test');