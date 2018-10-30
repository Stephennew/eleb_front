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

Route::get('/', function () {
    return view('welcome');
});
Route::get('/list','ApiController@list');

//商家 route
Route::prefix('shop')->group(function (){
    Route::get('businessList','ShopsController@businessList');
    Route::get('business','ShopsController@business');
});
//会员登录注册 route
Route::prefix('member')->group(function (){
   Route::post('logincheck','SessionController@loginCheck');
   Route::post('register','SessionController@register');
   //正常情况下发短信都是post请求，这里是前端写好的所以用的是get方式
   Route::get('sms','SmsContorller@sendSms');
   Route::post('changePassword','SessionController@changePassword');
   Route::post('forgetPassword','SessionController@forgetPassword');
});

//地址管理
Route::prefix('address')->group(function (){
    Route::post('addAddress','AddressController@addAddress');
    Route::get('addressList','AddressController@addressList');
    Route::get('editView','AddressController@editView');
    Route::post('editAddress','AddressController@editAddress');
});

//购物车管理
Route::prefix('cart')->group(function (){
    Route::post('addCart','CartController@addCart');
    Route::get('cartView','CartController@cartView');
});

//订单管理 roure
Route::prefix('order')->group(function (){
   Route::post('addOrder','OrderController@addOrder');
   Route::get('order','OrderController@order');
   Route::get('orderList','OrderController@orderList');
});
