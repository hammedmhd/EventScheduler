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

Route::get('/', 'ClientController@index')->name('home');

Route::get('/eventSources', 'ClientController@eventsRender');

Route::post('/newclient', 'ClientController@store');

Route::get('/updateClient', 'ClientController@update');

Route::delete('/removeClient', 'ClientController@delete');

/*should be post by convention*/
Route::get('/newEvent', 'LessonController@store');

/*should be patch by convention*/
Route::get('/updateEvent', 'LessonController@update');

Route::get('/updatePayment', 'LessonController@updatePayment');

/*should be delete by convention*/
Route::get('/removeEvent', 'LessonController@delete');

Route::get('/viewExpenses', 'ExpensesController@viewExpenses');