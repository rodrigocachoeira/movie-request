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

Auth::routes();

Route::get('/', function () {
    return redirect('/movies');
});

Route::get('movies', 'MoviesController@index')->name('movies');

Route::post('movies/{movie}/like', 'EvaluationsController@like');
Route::post('movies/{movie}/dislike', 'EvaluationsController@dislike');