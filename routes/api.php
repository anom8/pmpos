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

// Route::get('/api', function (Request $request) {
//     return $request->user();
// })->middleware('auth:api');

Route::group(['prefix' => '/v1', 'middleware' => ['cors']], function() {
	Route::get('/', function () {
	    \App\Models\User::create([
 
           'name' => 'test',
           'email' => 'test@mbpos.com',
           'api_token' => md5(date('YmdHis').str_random(60)), //slight change here
           'password' => bcrypt('1234'),
           'role_id' => 2,
 
       ]);
	});

	// POST
	Route::post('login', array('uses' => 'Api\UserController@login'));

	// Route::group(['middleware' => ['auth:api']], function() {
	Route::group(['middleware' => []], function() {
		//GET
		Route::get('product', array('uses' => 'Api\ProductController@getList'));
		Route::get('product/rfid', array('uses' => 'Api\ProductController@getListRfid'));
		Route::get('table', array('uses' => 'Api\TableController@getList'));
		Route::get('table/detail/{id}', array('uses' => 'Api\TableController@getDetail'));
		Route::get('table/current', array('uses' => 'Api\TableController@getCurrent'));
		
		// 
		Route::post('transaction/add', array('uses' => 'Api\TransactionController@addTransaction'));
		Route::post('transaction/update', array('uses' => 'Api\TransactionController@updateTransaction'));
		Route::post('transaction/finish', array('uses' => 'Api\TransactionController@finishTransaction'));
		Route::get('transaction/history', array('uses' => 'Api\TransactionController@historyTransaction'));
	});
});
