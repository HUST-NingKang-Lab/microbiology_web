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
Route::get('/getProjectNum', 'ProjectController@getProjectNum');

Route::get('/getSampleList', 'SampleController@getSampleList');
Route::get('/getSampleInfo', 'SampleController@getSampleInfo');

Route::get('/getRunList', 'RunController@getRunList');
Route::get('/getRunInfo', 'RunController@getRunInfo');
Route::get('/getRunQC','RunController@getRunQC');
Route::get('/getRunTaxonomy','RunController@getRunTaxonomy');
Route::get('/getRunGO','RunController@getRunGO');

Route::post('/metaStormsSearch', 'SearchController@metaStormsSearch');

Route::get('/test', 'Test@test');