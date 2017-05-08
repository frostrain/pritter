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

Route::get('/', [
    'as' => 'home',
    'uses' => 'HomeController@index',
]);

Route::get('/lists', [
    'as' => 'lists',
    'uses' => 'HomeController@lists',
]);

Route::get('/handle', [
    'as' => 'handle',
    'uses' => 'HomeController@handle',
]);

Route::get('/translation', [
    'as' => 'translation',
    'uses' => 'HomeController@translation',
]);

Route::get('/convert', 'HomeController@convert');
