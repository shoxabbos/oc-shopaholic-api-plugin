<?php
use Illuminate\Http\Request;
use RainLab\User\Models\User as UserModel;
use Lovata\Toolbox\Classes\Helper\UserHelper;
use Shohabbos\Shopaholicapi\Resources\UserResource;

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
    Route::post('user/orders/create', 'Shohabbos\Shopaholicapi\Controllers\Order@makeOrder');

    Route::post('user/update', 'Shohabbos\Shopaholicapi\Controllers\User@update');
    Route::post('user/set-device-conf', 'Shohabbos\Shopaholicapi\Controllers\User@setDeviceConf');


});






Route::get('api/sociallogin/{provider}/auth', function (Request $request, $provider_name) {
    $access_token = Input::get('access_token');

    \Log::debug([
        'request' => $request->header(),
        'input' => \Input::all(),
    ]);

    $provider_class = \Flynsarmy\SocialLogin\Classes\ProviderManager::instance()
        ->resolveProvider($provider_name);

    if ( !$provider_class )
        return Redirect::to($error_redirect)->withErrors("Unknown login provider: $provider_name.");

    $provider = $provider_class::instance();
    $adapter = $provider->getAdapter();

    $adapter->setAccessToken(['access_token' => $access_token]);
    $token = $adapter->getAccessToken();
    $profile = $adapter->getUserProfile();
    $adapter->disconnect();

    $provider_response = [
        'token' => $token,
        'profile' => $profile
    ];


    ksort($provider_response['token']);

    $provider_details = [
        'provider_id' => $provider_name,
        'provider_token' => $provider_response['token'],
    ];
    $user_details = $provider_response['profile'];

    if (isset($user_details->email) || empty($user_details->email)) {
        $user_details->email = $user_details->identifier."@pmall.uz";
    }
    
    // Grab the user associated with this provider. Creates or attach one if need be.
    $user = \Flynsarmy\SocialLogin\Classes\UserManager::instance()->find(
        $provider_details,
        $user_details
    );

    // Support custom login handling
    $result = Event::fire('flynsarmy.sociallogin.handleLogin', [
        $provider_details, $provider_response, $user
    ], true);


    if ( $result ) {
        return $result;
    }

    Auth::login($user);
    $tokennn = JWTAuth::fromUser($user);

    return [
        'data' => new UserResource($user),
        'token' => $tokennn,
        'success' => 'Добро пожаловать, '.$user->name
    ];
});


