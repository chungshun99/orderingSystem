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




// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', 'HomeController@index');

Auth::routes();

//disable register page
//Auth::routes(['register' => false]);

Route::get('/home', 'HomeController@index')->name('home');

Route::middleware(['auth']) -> group(function() {
    
    //use function instead of controller, return view instead of controller
    //routes for cashier
    Route::get('/cashier', 'Cashier\CashierController@index');

    Route::get('/cashier/getTable', 'Cashier\CashierController@getTables');

    Route::get('/cashier/getMenuByCategory/{category_id}', 'Cashier\CashierController@getMenuByCategory');

    Route::get('/cashier/getSalesDetailsByTable/{table_id}', 'Cashier\CashierController@getSalesDetailsByTable');

    Route::post('/cashier/orderFood', 'Cashier\CashierController@orderFood');

    Route::post('/cashier/confirmOrderStatus', 'Cashier\CashierController@confirmOrderStatus');

    Route::post('/cashier/deleteSalesDetail', 'Cashier\CashierController@deleteSalesDetail');

    Route::post('/cashier/savePayment', 'Cashier\CashierController@savePayment');

    Route::get('/cashier/showReceipt/{sales_id}', 'Cashier\CashierController@showReceipt');

    Route::post('/cashier/increaseQuantity', 'Cashier\CashierController@increaseQuantity');

    Route::post('/cashier/decreaseQuantity', 'Cashier\CashierController@decreaseQuantity');
});

Route::middleware(['auth', 'VerifyAdmin']) -> group(function() {
    //use function instead of controller, return view instead of controller
    //route for management
    Route::get('/management', function(){
        return view('management.index');
    });

    Route::resource('management/category', 'Management\CategoryController');
    Route::resource('management/menu', 'Management\MenuController');
    Route::resource('management/table', 'Management\TableController');
    Route::resource('management/user', 'Management\UserController');

    //routes for report
    Route::get('/report', 'Report\ReportController@index');

    Route::get('/report/show', 'Report\ReportController@show');

    Route::get('/report/show/export', 'Report\ReportController@export');

});



