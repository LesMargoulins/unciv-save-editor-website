<?php

use Illuminate\Support\Facades\Route;

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

## Landing
Route::get('/', 'LandingController@index');

## Editor
Route::get('/editor', 'EditorController@index');
Route::post('/editor', 'EditorController@process_save');

## Mods
Route::get('/mods', 'ModsController@index');