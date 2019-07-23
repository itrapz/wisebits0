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
Route::put('/country', ['uses' => 'CountryController@update', 'as'=>'country.update']);
Route::get('/countries', ['uses' => 'CountryController@index', 'as'=>'countries']);
