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
Route::post('/taxonomyAnalysis', 'SearchController@taxonomyAnalysis');


Route::get('/test', 'Test@test');

//Resource
Route::get('/hidden.png', function (Request $request) {
//    return getcwd();
    return file_get_contents('hidden.png');
});
Route::get('/meta_viewer.js', function (Request $request) {
//    return getcwd();
    return file_get_contents('meta_viewer.js');
});