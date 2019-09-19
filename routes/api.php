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

Route::get('/photos', 'PhotoController@index');
Route::get('/stations', 'StationController@index');
Route::get('/phylum', 'PhylumController@index');
Route::get('/occurrences', 'OccurrenceController@index');
Route::get('/classes', 'ClassController@index');
Route::get('/orders', 'OrderController@index');
Route::get('/family', 'FamilyController@index');
Route::get('/species', 'SpeciesController@index');
Route::get('/water-quality', 'WaterQualityController@index');
Route::get('/records/{id}', 'RecordController@page');
Route::get('/element-flux', 'WaterQualityController@elementFlux');
Route::get('/temperature', 'TemperatureController@index');
Route::get('/algae-debris', 'AlageController@index');
Route::get('/river-discharge-estimation', 'FlowController@index');

