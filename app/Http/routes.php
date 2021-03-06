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

Route::get('NineNine', 'NineNineController@index');

Route::get('Go', 'GoController@Index');

Route::get('Go/{n?}', 'GoController@Normal')->where('n', '[0-9]+');


Route::get('Go/SimAuto/{n?}/{step?}', 'GoController@SimAuto')
								->where('n', '[0-9]+')
								->where('step', '[0-9]+');
Route::get('Go/SimByStep/RequestNext', 'GoController@RequestNext');


Route::get('Go/HCC/CheckValidState', 'GoController@CheckValidState');
//Route::get('Go/HCC/HCCRequestNext', 'GoController@HCCRequestNext');
Route::get('Go/HCC/{n?}', 'GoController@HumanComputer');


Route::get('Go/SimAuto/Count', 'GoController@Count');
Route::get('Go/SimAuto/Store', 'GoController@Store');
Route::get('Go/SimAuto/Show/{id}', 'GoController@Show');

Route::get('try/{a}', function($a){
	//return "t-$a-$b";
	$b = [];
	$b[] = $a;
	test($b);

	dd($b);
});

Route::get('try1/{a}/{b}', function($a, $b){
	phpinfo();
});


function test(&$a)
{
	test2($a);
}

function test2(&$a)
{
	//$a[] = 105;
	array_push($a, 108);
}
