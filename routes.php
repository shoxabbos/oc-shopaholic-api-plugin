<?php
use RainLab\User\Models\User as UserModel;
use Lovata\Toolbox\Classes\Helper\UserHelper;

Route::group([
    'prefix' => 'api', 
    'middleware' => [
        'Shohabbos\Shopaholicapi\Middleware\Logger',
        'Shohabbos\Shopaholicapi\Middleware\LanguageDetector' // only if you have installed RainLab.Translate plugin
    ]
], function () {

    //
    // User methods
    //
    Route::post('auth/signin', 'Shohabbos\Shopaholicapi\Controllers\Auth@signin');
    Route::post('auth/signup', 'Shohabbos\Shopaholicapi\Controllers\Auth@signup');
    Route::post('auth/refresh-token', 'Shohabbos\Shopaholicapi\Controllers\Auth@refresh');
    Route::post('auth/invalidate-token', 'Shohabbos\Shopaholicapi\Controllers\Auth@invalidate');
    Route::post('auth/restore-password', 'Shohabbos\Shopaholicapi\Controllers\Auth@restorePassword'); // step 1
    Route::post('auth/reset-password', 'Shohabbos\Shopaholicapi\Controllers\Auth@resetPassword'); // step 2



    //
    // Ready to use
    //
    Route::get('/products', 'Shohabbos\Shopaholicapi\Controllers\ProductList@index');
    Route::get('/product/{id}', 'Shohabbos\Shopaholicapi\Controllers\ProductList@page');
    Route::get('/product/{id}/reviews', 'Shohabbos\Shopaholicapi\Controllers\ProductList@reviews');
    Route::get('/products/byid', 'Shohabbos\Shopaholicapi\Controllers\ProductList@filterByid');

    Route::get('/brands', 'Shohabbos\Shopaholicapi\Controllers\BrandList@index');
    Route::get('/brand/{id}', 'Shohabbos\Shopaholicapi\Controllers\BrandList@page');

    Route::get('/currencies', 'Shohabbos\Shopaholicapi\Controllers\CurrencyList@index');
    Route::get('/currency/{id}', 'Shohabbos\Shopaholicapi\Controllers\CurrencyList@page');

    Route::get('/categories', 'Shohabbos\Shopaholicapi\Controllers\CategoryList@index');
    Route::get('/category/{id}', 'Shohabbos\Shopaholicapi\Controllers\CategoryList@page');
    Route::get('/category/{id}/children', 'Shohabbos\Shopaholicapi\Controllers\CategoryList@children');

    Route::get('/stores', 'Shohabbos\Shopaholicapi\Controllers\StoreList@index');
    Route::get('/store/{id}', 'Shohabbos\Shopaholicapi\Controllers\StoreList@page');
    Route::get('/store/{id}/products', 'Shohabbos\Shopaholicapi\Controllers\StoreList@storeProducts');

    Route::get('/paymentmethodlist', 'Shohabbos\Shopaholicapi\Controllers\PaymentMethodList@index');
    Route::get('/shippingtypelist', 'Shohabbos\Shopaholicapi\Controllers\ShippingTypeList@index');




 








    // collections
    Route::get('/banners/{type}', 'Shohabbos\Shopaholicapi\Controllers\BannerList@index');    
    Route::post('/currencies/switch', 'Shohabbos\Shopaholicapi\Controllers\CurrencyList@switch');
    Route::get('/tags', 'Shohabbos\Shopaholicapi\Controllers\TagList@index');
    Route::get('/labels', 'Shohabbos\Shopaholicapi\Controllers\LabelList@index');


    //
    // Private methods
    //
    Route::post('/statuslist', 'Shohabbos\Shopaholicapi\Controllers\StatusList@page');

    Route::get('/order/get/{slug}', 'Shohabbos\Shopaholicapi\Controllers\Order@get');
    Route::post('/order/create', 'Shohabbos\Shopaholicapi\Controllers\Order@create');
    Route::post('/review/create', 'Shohabbos\Shopaholicapi\Controllers\Review@create');
});



Route::group([
    'prefix' => 'api',
    'middleware' => [
        'Shohabbos\Shopaholicapi\Middleware\Logger',
        '\Tymon\JWTAuth\Middleware\GetUserFromToken',
        'Shohabbos\Shopaholicapi\Middleware\LanguageDetector'
    ]
], function() {
    // private methods of user
    Route::get('user/get', 'Shohabbos\Shopaholicapi\Controllers\User@get');
    Route::get('user/orders', 'Shohabbos\Shopaholicapi\Controllers\User@orders');

    Route::post('user/update', 'Shohabbos\Shopaholicapi\Controllers\User@update');
    Route::post('user/set-device-conf', 'Shohabbos\Shopaholicapi\Controllers\User@setDeviceConf');


});
