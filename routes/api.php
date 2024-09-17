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

Route::get('/references', 'ReferenceController@index');
Route::get('/photos', 'PhotoController@index');
Route::get('/stations', 'StationController@index');
Route::get('/stations/{id}', 'StationController@get');
Route::get('/stations-location', 'StationController@map');
Route::get('/phylum', 'PhylumController@index');
Route::get('/occurrences', 'OccurrenceController@index');
Route::get('/occurrences/{id}', 'OccurrenceController@show');
Route::get('/occurrences-list-map', 'OccurrenceController@listmap');
Route::get('/occurrences-report', 'OccurrenceController@report');
Route::get('/occurrences-years', 'OccurrenceController@years');
Route::get('/classes', 'ClassController@index');
Route::get('/orders', 'OrderController@index');
Route::get('/family', 'FamilyController@index');
Route::get('/species', 'SpeciesController@index');
Route::get('/water-quality', 'WaterQualityController@index');
Route::get('/water-quality-report', 'WaterQualityController@report');
Route::get('/records/{id}', 'RecordController@page');
Route::get('/element-flux', 'WaterQualityController@elementFlux');
Route::get('/temperature', 'TemperatureController@index');
Route::get('/temperature-report', 'TemperatureController@report');
Route::get('/algae-debris', 'AlageController@index');
Route::get('/river-discharge-estimation', 'FlowController@index');
Route::get('/river-discharge-estimation-report', 'FlowController@report');
Route::get('/river-habitat', 'RiverController@habitat');
Route::get('/river-habitat-report', 'RiverController@habitatReport');
Route::get('/river-section', 'RiverController@section');
Route::get('/river-sections/{id}/{date}', 'RiverController@certainSection');
Route::get('/simpling-events', 'SimplingEventController@index');
Route::get('/projects/{projectId}/stations/{stationId}/simpling-events', 'SimplingEventController@events');
Route::get('/env-microplastics', 'MicroplasticController@index');
Route::get('/env-microplastics/{id}', 'MicroplasticController@get');
Route::get('/bio-microplastics', 'MicroplasticController@bioIndex');
Route::get('/bio-microplastics/{id}', 'MicroplasticController@getBio');
