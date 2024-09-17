<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('app');
});

Route::get('/{any}', function () {
    return view('app');
});

Route::get('/projects/{projectId}/stations/{stationId}/simpling-events', function () {
    return view('app');
});

Route::get('/occurrences/{id}', function () {
    return view('app');
});

Route::get('/records/{id}', function () {
    return view('app');
});

Route::get('/river-sections/{id}/{date}', function () {
    return view('app');
});

Route::get('/env-microplastics/{id}', function () {
    return view('app');
});

Route::get('/bio-microplastics/{id}', function () {
    return view('app');
});
