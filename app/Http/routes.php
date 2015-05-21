<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'WelcomeController@index');

Route::get('home', 'HomeController@index');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);

Route::get('NineNine', 'NineNineController@index');

Route::get('Go/{n?}', 'GoController@index')->where('n', '[0-9]+');	

Route::get('Go/Sim/{n?}/{step?}', 'GoController@Sim')
								->where('n', '[0-9]+')
								->where('step', '[0-9]+');

Route::get('Go/SimByStep/{n?}/{step?}', 'GoController@SimByStep')
								->where('n', '[0-9]+')
								->where('step', '[0-9]+');

Route::get('Go/SimByStep/RequestNext', 'GoController@RequestNext');

Route::get('test/{a}/{b}', 'GoController@Test');
