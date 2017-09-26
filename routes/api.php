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
Route::get('/getProjectBiome', 'ProjectController@getProjectBiome');


Route::get('/getSampleList', 'SampleController@getSampleList');
Route::get('/getSampleInfo', 'SampleController@getSampleInfo');
Route::get('getTotalNumberOfSamples','SampleController@getTotalNumberOfSamples');


Route::get('/getRunList', 'RunController@getRunList');
Route::get('/getRunInfo', 'RunController@getRunInfo');
Route::get('/getRunQC','RunController@getRunQC');
Route::get('/getRunTaxonomy','RunController@getRunTaxonomy');
Route::get('/getRunGO','RunController@getRunGO');
Route::get('/getRunResults','RunController@getRunResults');
Route::get('/getTotalNumberOfRuns','RunController@getTotalNumberOfRuns');
Route::get('/getRunsWithGO','RunController@getRunsWithGO');
Route::get('/getRunsWithTaxonomy','RunController@getRunsWithTaxonomy');
Route::post('/getGOHeatMap','RunController@getGOHeatMap');
Route::get('/getTaxonomyHeatMap','RunController@getGOHeatMap');
Route::post('/getGOOfRuns','RunController@getGOOfRuns');


Route::post('/metaStormsSearch', 'SearchController@metaStormsSearch');
Route::post('/taxonomyAnalysis', 'SearchController@taxonomyAnalysis');
Route::get('/getTaskStatus','SearchController@getTaskStatus');
Route::get('downloadTaxonomyAnalysis','SearchController@downloadTaxonomyAnalysis');

Route::get('/getBiomes','ClassificationController@getBiomes');



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