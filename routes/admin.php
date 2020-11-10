<?php
Route::get('language/{id}', function ($id) {
    session(['back-lang' => $id]);
    return redirect()->back();
})->name('language');

Route::group(['namespace' => 'Admin'], function () {
    Route::get('login', 'AdminController@login')->name('login');
    Route::post('login', 'AdminController@loginPost')->name('login.post');
    Route::get('admin-forgot-password', 'AdminController@adminShowForgotPassword')->name('forgot-password');
    Route::post('admin-forgot-password', 'AdminController@adminForgotPassword')->name('forgot-password.post');
    Route::get('reset/password/{token}', 'AdminController@showResetForm')->name('reset.show');
    Route::post('reset-password', 'AdminController@resetPassword')->name('reset-password.post');

    Route::get('404', function () {
        return view('backend.404');
    })->name('404');
    Route::get('403', function () {
        return view('backend.access-denied');
    })->name('403');
    Route::get('500', function () {
        return view('backend.500');
    })->name('500');

    Route::group(['middleware' => 'admin'], function () {
        Route::get('/', 'AdminController@index')->name('home');
        Route::get('/dashboard', 'AdminController@dashboard')->name('dashboard');
        Route::resource('roles', 'RoleController');
        Route::get('/logout', 'AdminController@logout')->name('logout');
        Route::get('/profile', 'AdminController@profile')->name('profile');
        Route::post('/profile', 'AdminController@profilePost')->name('profile.post');

        /*Branch*/
        Route::resource('branch', 'BranchController');
        Route::get('branch/{uuid}/delete', 'BranchController@delete')->name('branch.delete');
        Route::post('branch-paginate', 'BranchController@paginate')->name('branch.paginate');

        /*Users*/
        Route::get('users/{uuid}/delete', 'UserController@delete')->name('users.delete');
        Route::get('users/{uuid}/permissions', 'UserController@userPermissons')->name('users.permissions');
        Route::post('users/{uuid}/permissions', 'UserController@permissionStore')->name('users.permissions.store');
        Route::post('users-paginate', 'UserController@paginate')->name('users.paginate');
        Route::resource('users', 'UserController');

        Route::resource('customer', 'CustomerController');
        Route::post('customer-paginate', 'CustomerController@paginate')->name('customer.paginate');
        Route::get('customer/{uuid}/delete', 'CustomerController@delete')->name('customer.delete');
        Route::post('customer/addressstore', 'CustomerController@addressStore')->name('customer.addressstore');
        Route::post('customer/addressupdate/{uuid}', 'CustomerController@addressUpdate')->name('customer.addressupdate');

        Route::get('branch/{uuid}/delete', 'BranchController@delete')->name('category.delete');

        Route::resource('attributes', 'AttributesController');
        Route::get('attributes/{uuid}/delete', 'AttributesController@delete')->name('attributes.delete');

        Route::resource('modifier', 'ModifierController');
        Route::get('modifier/{uuid}/delete', 'ModifierController@delete')->name('modifier.delete');

        Route::resource('category', 'CategoryController');
        Route::get('category/{uuid}/delete', 'CategoryController@delete')->name('category.delete');

        Route::resource('price_type', 'PriceTypeController');
        Route::get('price_type/{uuid}/delete', 'PriceTypeController@delete')->name('price_type.delete');

        Route::resource('table', 'TableController');
        Route::get('table/{uuid}/delete', 'TableController@delete')->name('table.delete');
        Route::get('table/generateQr/{uuid}', 'TableController@generateQr')->name('table.generate-Qr');

        Route::post('product-paginate', 'ProductController@paginate')->name('product.paginate');
        Route::get('product/{uuid}/delete', 'ProductController@delete')->name('product.delete');
        Route::get('product/delete-image/{id}', 'ProductController@ImageDelete')->name('product.delete-image');
        Route::get('product/category-attribute/{id}', 'ProductController@getcategoryattribute')->name('product.category-attribute');
        Route::resource('product', 'ProductController');

        Route::resource('printer', 'PrinterController');
        Route::get('printer/{uuid}/delete', 'PrinterController@delete')->name('printer.delete');

        Route::resource('kitchen', 'KitchenController');
        Route::get('kitchen/{uuid}/delete', 'KitchenController@delete')->name('kitchen.delete');
        Route::get('kitchen/getPrinter/{branch_id}', 'KitchenController@getPrinter')->name('kitchen.delete');

        Route::resource('voucher', 'VoucherController');
        Route::get('voucher/{uuid}/delete', 'VoucherController@delete')->name('voucher.delete');
        Route::post('voucher-paginate', 'VoucherController@paginate')->name('voucher.paginate');

        Route::resource('terminal', 'TerminalController');
        Route::get('terminal/{uuid}/delete', 'TerminalController@delete')->name('terminal.delete');

        Route::resource('product_inventory', 'ProductStoreInventoryController');
        Route::get('product_inventory/{id}/product_branch', 'ProductStoreInventoryController@getProductBranch');
        Route::get('product_inventory/{id}/product_rac', 'ProductStoreInventoryController@getBranchRac');
        Route::get('product_inventory/{id}/product_rac_box', 'ProductStoreInventoryController@getBranchRacBox');
		Route::get('product_inventory/{uuid}/delete', 'ProductStoreInventoryController@delete')->name('product_inventory.delete');
        Route::resource('setting', 'SystemSettingController');

        Route::resource('banner', 'BannerController');

        Route::resource('category_attribute', 'CategoryAttributesController');
        Route::get('category_attribute/{uuid}/delete', 'CategoryAttributesController@delete')->name('category_attribute.delete');

        Route::resource('tax', 'TaxController');
        Route::get('tax/{uuid}/delete', 'TaxController@delete')->name('tax.delete');

        Route::resource('logs', 'LogsController');
        Route::post('logs-paginate', 'LogsController@paginate')->name('logs.paginate');

        Route::resource('attendance', 'AttendanceController');
        Route::post('attendance-paginate', 'AttendanceController@paginate')->name('attendance.paginate');
        Route::post('attendance-export', 'AttendanceController@exportData')->name('attendance.export');

        Route::resource('rac', 'RacController');
        Route::post('rac-paginate', 'RacController@paginate')->name('rac.paginate');
        Route::get('rac/{uuid}/delete', 'RacController@dselete')->name('rac.delete');

        Route::resource('box', 'BoxController');
        Route::post('box-paginate', 'BoxController@paginate')->name('box.paginate');
        Route::get('box/{uuid}/delete', 'BoxController@delete')->name('box.delete');
        Route::get('box/{branch_id}/list', 'BoxController@racByBranch')->name('box.list');

        Route::get('reports-customer-index', 'ReportsController@customerIndex')->name('reports.customer.index');
        Route::post('reports-customer-paginate', 'ReportsController@customerPaginate')->name('reports.customer.paginate');
        Route::post('reports-customer-export', 'ReportsController@customerExportData')->name('reports.customer.export');

        Route::resource('setmeal','SetMealController');
		Route::get('setmeal/{uuid}/delete', 'SetMealController@delete')->name('setmeal.delete');

        Route::resource('payment-type','PaymentTypeController');
        Route::get('payment-type/{uuid}/delete', 'PaymentTypeController@delete')->name('payment-type.delete');

        Route::post('order-paginate', 'OrderController@paginate')->name('order.paginate');
        Route::resource('order', 'OrderController');
		
		Route::resource('wine_store_management', 'CustomerLiquorInventoryController');
		
		Route::resource('country', 'CountriesController');
        Route::get('country/{id}/delete', 'CountriesController@delete')->name('country.delete');

        Route::resource('state', 'StatesController');
        Route::get('state/{id}/delete', 'StatesController@delete')->name('state.delete');
        Route::get('state/{country_id}/list', 'StatesController@stateByCountry')->name('state.list');

        Route::resource('city', 'CitiesController');
        Route::get('city/{id}/delete', 'CitiesController@delete')->name('city.delete');
        Route::get('city/{state_id}/list', 'CitiesController@cityByState')->name('city.list');

    });
});
