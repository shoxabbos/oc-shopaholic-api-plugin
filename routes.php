<?php

Route::group(['prefix' => 'api'], function () {

    
    // collections
    Route::get('/products', 'Shohabbos\Shopaholicapi\Controllers\ProductList@index');
    Route::get('/products/{id}', 'Shohabbos\Shopaholicapi\Controllers\ProductList@page');

    Route::get('/brands', 'Shohabbos\Shopaholicapi\Controllers\BrandList@index');
    Route::get('/brands/{id}', 'Shohabbos\Shopaholicapi\Controllers\BrandList@page');

    Route::get('/categories', 'Shohabbos\Shopaholicapi\Controllers\CategoryList@index');
    Route::get('/categories/{id}', 'Shohabbos\Shopaholicapi\Controllers\CategoryList@page');
    Route::get('/categories/{id}/children', 'Shohabbos\Shopaholicapi\Controllers\CategoryList@children');


});