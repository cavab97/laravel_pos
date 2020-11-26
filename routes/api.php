<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1/{locale}', 'namespace' => 'Api'], function () {
    Route::post('configs', 'ApiController@configs');
    Route::get('test', 'ApiController@test');
    Route::post('login', 'ApiController@login');
    Route::post('verifyPIN', 'ApiController@verifyPIN');
    Route::post('verifyTerminalkey','ApiController@verifyTerminalKey');
    Route::get('getPermissionList','ApiController@getPermissionList');
    Route::post('synch-table','SynchronizeController@actionTableData');
    Route::post('app-data-table','SynchronizeController@appDataTable');

    Route::post('branch-user-role-datatable','SynchronizeController@appBranchUserRoleDataTable');
    Route::post('product-category-datatable','SynchronizeController@appProductCategoryDataTable');
    Route::post('product-variant-datatable','SynchronizeController@appProductVariantDataTable');
    Route::post('printer-price-type-datatable','SynchronizeController@appPrinterPriceTypeDataTable');
	Route::post('country-state-city-datatable','SynchronizeController@appCountryStateCityDataTable');
    Route::post('customer-terminal-payment-datatable','SynchronizeController@appCustomerTerminalPaymentDataTable');
    Route::post('order-datatable','SynchronizeController@appOrderDataTable');
    Route::post('shift-datatable','SynchronizeController@appShiftDataTable');
    Route::post('productimage','SynchronizeController@productImage');

    /* For Sync Order Data */
    Route::post('create-order-data','SyncOrderController@createBulkOrders');
    Route::post('open-shift','SyncOrderController@openShift');
    Route::post('create-shift-data','SyncOrderController@createShift');
    Route::post('create-shift-detail-data','SyncOrderController@createShiftDetails');
    Route::post('create-terminal-log-data','SyncOrderController@createTerminalLog');
    Route::post('create-cancel-order-data','SyncOrderController@creatbulkorderscancel');

    Route::post('update-product-inventory-data','SyncOrderController@updateStockInventory');

	Route::post('update-customer-liquor-inventory-data','SyncOrderController@updateCustomerLiquorInventory');

	Route::post('create-customer-data','SyncOrderController@createCustomer');

    /* For Attendance API*/
    Route::post('verify-attendance-terminal-key','AttendanceController@verifyAttendanceTerminalKey');
    Route::post('synch-user-role','AttendanceController@syncRoleUserTableData');
    Route::post('synch-attendance-data','AttendanceController@syncAttendanceTableData');
    Route::post('single-table-data','AttendanceController@singleTableData');

    /* Web Order Sync */
    Route::post('web-order-table-data','SynchronizeController@synchWebOrderData');

    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('profile', 'ApiController@profile');

    });
});
