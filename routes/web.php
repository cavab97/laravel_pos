<?php

use Illuminate\Support\Facades\Route;

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
Route::get('/', 'HomeController@index')->name('index');
Route::get('/home', 'HomeController@index')->name('home');

Route::get('/scan-qr/{uuid}', 'HomeController@scanQr')->name('scan-qr');
Route::get('/category/{slug}', 'HomeController@categoryList')->name('category');
Route::get('/product/{branch}/{category}', 'HomeController@productList')->name('product');
Route::post('product-listing', 'HomeController@searchProduct')->name('product-listing');
Route::get('/product/{uuid}/{slug}/popup', 'HomeController@productDetails')->name('product.details');
Route::post('/addtocart','HomeController@addToCart')->name('cart.addToCart');
Route::get('/cart-checkout/{slug}','HomeController@cartCheckOut')->name('cart.cartCheckOut');
Route::post('/cart/update','HomeController@updateCart')->name('cart.update');
Route::post('cart/remove','HomeController@removeCart')->name('cart.remove');
Route::get('cart/remove_confirm/{id}/{slug}','HomeController@removeCartPopup')->name('cart.removePopup');
Route::post('cart/check-voucher','HomeController@checkVoucher')->name('cart.checkVoucher');
Route::post('cart/apply-voucher','HomeController@applyVoucher')->name('cart.applyVoucher');
Route::get('/cart/payment-option/{slug}/{mobile}/{email?}', 'HomeController@paymentOptions')->name('cart.paymentOptions');
Route::post('/cart/create-order','HomeController@createOrder')->name('cart.createOrder');
Route::get('/order-success/{order_number}/{uuid}', 'HomeController@orderSuccess')->name('orderSuccess');

Route::post('/branch/check-cart-product','HomeController@checkBranchProductCart')->name('branch.cartProduct');
Route::get('/branch/cart-product-remove-popup/{slug}/{msg}','HomeController@cartProductRemovePopup')->name('branch.cartProductRemovePopup');
Route::post('/branch/clear-cart-product','HomeController@clearCartBranchProduct')->name('branch.clearCartBranchProduct');

Route::get('/setmeal-product/{uuid}/{slug}/popup', 'HomeController@setmealProductDetails')->name('setmeal-product.details');

Route::get('/login', 'LoginController@login')->name('login');
Route::post('/login', 'LoginController@loginPost')->name('login.post');
Route::get('/signup', 'LoginController@signup')->name('signup');
Route::post('/signup', 'LoginController@signupPost')->name('signup.post');
Route::get('/logout', 'LoginController@logout')->name('logout');
Route::get('/forgot-password', 'ForgotPasswordController@forgotPassword')->name('forgotPassword');
Route::post('forgot-password', 'ForgotPasswordController@forgotPasswordPost')->name('forgotPassword.post');
Route::get('reset-password/{token}', 'ForgotPasswordController@resetPassword')->name('reset-password.token');
Route::post('reset-password/{token}', 'ForgotPasswordController@resetPasswordUpdate')->name('reset-password.update');
Route::get('/about-us', 'HomeController@aboutUs')->name('aboutUs');
Route::get('/contact-us', 'HomeController@contactUs')->name('contactUs');
Route::post('/contact-us', 'HomeController@contactUsPost')->name('contactUs.post');

Route::group(['middleware' => 'front'], function () {
    Route::get('/order-list/{slug}', 'HomeController@orderList')->name('orderList');
});

Route::get('/clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    return "Cleared!";
});
Route::get('/migrate', function () {
    return Artisan::call('migrate');
});

Route::get('/migrate/pro', function () {
    return Artisan::call('migrate --force');
});

Route::get('/seeder', function () {
    return Artisan::call('db:seed');
});
