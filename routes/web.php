<?php
use Illuminate\Http\Request;
use App\Library\TMoney;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

// Route::get('/', function () {
//     return view('posts');
// });

if(env('APP_ENV', 'local') == 'local')
    $exitCode = Artisan::call('view:clear');

Route::get('/encrypt/{val}', function ($val) {
    return encrypt($val);
//     return view('posts');
});

// Route::resource('/', 'HomeController@index');


// Route::resource('', 'HomeController');
// Route::resource('featured', ['uses' => 'FeaturedController', 'middleware' => 'role:admin']);

Route::get('/', array('uses' => 'HomeController@index', 'as' => '/'));
Route::get('/home', array('uses' => 'HomeController@index', 'as' => '/home'));
// Role Admin
Route::group(['middleware' => 'role:1'], function () {
    Route::resource('category', 'CategoryController');
    Route::resource('user', 'UserController');
    Route::resource('config', 'ConfigController');
    Route::resource('guest', 'GuestController');

    Route::get('user/{id}/ban', array('uses' => 'UserController@ban', 'as' => 'user.ban'));
    Route::get('user/{id}/points', array('uses' => 'UserController@points', 'as' => 'user.points'));
    Route::patch('user/{id}/savePoint', array('uses' => 'UserController@savePoint', 'as' => 'user.savePoint'));

    /* Feedback */
    Route::get('feedback', array('uses' => 'FeedbackController@index', 'as' => 'feedback.index'));
    Route::delete('feedback/{id}/destroy', array('uses' => 'FeedbackController@destroy', 'as' => 'feedback.destroy'));
});

Route::group(['middleware' => 'role:5'], function () {
    Route::post('open-balance', 'HomeController@openBalance');
    Route::get('close-balance', array('uses' => 'HomeController@closeBalance', 'as' => 'close-balance'));
    Route::get('print-stock-out', array('uses' => 'TransactionHistoryController@excelStockOut', 'as' => 'print-stock-out'));
    Route::get('print-report', array('uses' => 'TransactionHistoryController@excelReport', 'as' => 'print-report'));
    Route::get('print-csv-report', array('uses' => 'TransactionHistoryController@csvReport', 'as' => 'print-csv-report'));
    Route::get('print-daily-history', array('uses' => 'TransactionController@printDailyHistory', 'as' => 'print-daily-history'));
    Route::post('new-order', 'TransactionController@newOrder');
    Route::post('update-order/{id}', 'TransactionController@updateOrder');
    Route::post('void-order/{id}', 'TransactionController@voidOrder');
    Route::post('lost-order/{id}', 'TransactionController@lostOrder');
    Route::post('print-bill/{id}', 'TransactionController@printBill');
    Route::get('transaction/refresh-order', 'TransactionController@refreshCurrentOrderList');
    Route::resource('transaction', 'TransactionController');
    Route::resource('transaction-current', 'TransactionCurrentController');
    Route::get('transaction/create-with-table/{id}', array('uses' => 'TransactionController@createWithTable', 'as' => 'transaction.create_with_table'));
    Route::get('transaction-current', array('uses' => 'TransactionCurrentController@current', 'as' => 'transaction-current'));
    Route::delete('transaction-current/{id}/destroy_item', array('uses' => 'TransactionCurrentController@destroyItem', 'as' => 'transaction-current.destroy-item'));
    Route::patch('transaction-current/{id}/finish', array('uses' => 'TransactionCurrentController@finishTransaction', 'as' => 'transaction-current.finish'));
    Route::resource('transaction-history', 'TransactionHistoryController');
    Route::get('transaction-history/{id}/print', array('uses' => 'TransactionHistoryController@printTransaction', 'as' => 'transaction-history.print'));

    Route::resource('product', 'ProductController');
    Route::post('transaction/bill', 'TestController@bill');
    Route::post('transaction/finished', 'TestController@finished');
    Route::post('transaction/send', 'TestController@send');
});

// Role Advertiser with Admin can access
Route::group(['middleware' => 'role:2'], function () {

});


// Route::resource('login', 'Auth\LoginController');
Route::get('login', array('uses' => 'Auth\LoginController@index'));
Route::post('login', array('uses' => 'Auth\LoginController@doLogin'));
Route::get('logout', array('uses' => 'Auth\LoginController@getLogout', 'as' => 'logout'));

// Route::group(['prefix' => 'api', 'middleware' => 'cors'], function() {
// 	Route::get('/', function () {
// 	    echo "Welcome";
// 	});

// 	Route::get('product', array('uses' => 'Api\ProductController@getList'));
// 	Route::post('login', array('uses' => 'Api\UserController@login'));
// 	Route::post('transaction/add', array('uses' => 'Api\TransactionController@add_transaction'));
// 	Route::post('guest/add', array('uses' => 'Api\GuestController@add_guest'));
// });