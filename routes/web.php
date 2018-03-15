<?php

Route::any('/', 'UsersController@home');
Route::any('/info', 'UsersController@info');
Route::any('/teams', 'LocationsController@teams');
Route::any('/team', 'UsersController@archived');

Route::any('/u/{qr}', 'UsersController@qr');
Route::any('/l/{qr}', 'LocationsController@qr');


Route::any('/users/icon/{location}/{step?}/{team?}', 'UsersController@icon');

Route::any('/users/build/{location}/{step}', 'UsersController@build');

Route::any('/login', 'AdminController@login');

Route::middleware(['admin'])->group(function () {
	Route::any('/users', 'UsersController@index');
	Route::any('/users/unlock/{user}', 'UsersController@unlock');
	Route::any('/users/{user}/score/{element}/{amount}', 'UsersController@score');
	
	Route::any('/locations', 'LocationsController@index');
	Route::any('/locations/list', 'LocationsController@table');
	Route::any('/locations/add', 'LocationsController@add');
	Route::any('/locations/edit/{location}', 'LocationsController@edit');
	Route::any('/locations/icon/{location}', 'LocationsController@icon');
	Route::any('/locations/delete/{location}', 'LocationsController@delete');
});
