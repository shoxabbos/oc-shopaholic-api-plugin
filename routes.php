<?php
use RainLab\User\Models\User as UserModel;
use Lovata\Toolbox\Classes\Helper\UserHelper;

Route::group([
    'prefix' => 'api', 
    'middleware' => 'Shohabbos\Shopaholicapi\Middleware\ResponseMiddleware'
], function () {

    //
    // Public methods
    //


    // collections
    Route::get('/products', 'Shohabbos\Shopaholicapi\Controllers\ProductList@index');
    Route::get('/products/{id}', 'Shohabbos\Shopaholicapi\Controllers\ProductList@page');
    Route::post('/products/addtocompare', 'Shohabbos\Shopaholicapi\Controllers\ProductList@addtocompare');

    Route::get('/brands', 'Shohabbos\Shopaholicapi\Controllers\BrandList@index');
    Route::get('/brands/{id}', 'Shohabbos\Shopaholicapi\Controllers\BrandList@page');

    Route::get('/currencies', 'Shohabbos\Shopaholicapi\Controllers\CurrencyList@index');
    Route::get('/currencies/{id}', 'Shohabbos\Shopaholicapi\Controllers\CurrencyList@page');
    Route::post('/currencies/switch', 'Shohabbos\Shopaholicapi\Controllers\CurrencyList@switch');



    Route::get('/tags', 'Shohabbos\Shopaholicapi\Controllers\TagList@index');

    //Route::get('/labellist', 'Shohabbos\Shopaholicapi\Controllers\LabelList@index');
    Route::get('/shippingtypelist', 'Shohabbos\Shopaholicapi\Controllers\ShippingTypeList@index');
    Route::get('/paymentmethodlist', 'Shohabbos\Shopaholicapi\Controllers\PaymentMethodList@index');

    Route::get('/categories', 'Shohabbos\Shopaholicapi\Controllers\CategoryList@index');
    Route::get('/categories/{id}', 'Shohabbos\Shopaholicapi\Controllers\CategoryList@page');
    Route::get('/categories/{id}/children', 'Shohabbos\Shopaholicapi\Controllers\CategoryList@children');



    //
    // Private methods
    //
    Route::get('/cartlist/get', 'Shohabbos\Shopaholicapi\Controllers\CartList@get');
    Route::post('/cartlist/add', 'Shohabbos\Shopaholicapi\Controllers\CartList@add');
    Route::post('/cartlist/update', 'Shohabbos\Shopaholicapi\Controllers\CartList@update');
    Route::delete('/cartlist/remove', 'Shohabbos\Shopaholicapi\Controllers\CartList@remove');
    Route::post('/cartlist/restore', 'Shohabbos\Shopaholicapi\Controllers\CartList@restore');
    Route::post('/cartlist/clear', 'Shohabbos\Shopaholicapi\Controllers\CartList@clear');
    Route::post('/cartlist/setshippingtype', 'Shohabbos\Shopaholicapi\Controllers\CartList@setShippingType');
    Route::post('/cartlist/savedata', 'Shohabbos\Shopaholicapi\Controllers\CartList@saveData');


    Route::get('/order/get/{slug}', 'Shohabbos\Shopaholicapi\Controllers\Order@get');
    Route::post('/order/create', 'Shohabbos\Shopaholicapi\Controllers\Order@create');

    Route::post('/review/create', 'Shohabbos\Shopaholicapi\Controllers\Review@create');




    //
    // Auth methods
    //

    Route::post('auth/login', function (Request $request) {
        $credentials = [
            'email' => Request::get('email'),
            'password' => Request::get('password'),
        ];
        try {
            // verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        $userModel = JWTAuth::authenticate($token);
        if ($userModel->methodExists('getAuthApiSigninAttributes')) {
            $user = $userModel->getAuthApiSigninAttributes();
        } else {
            $user = [
                'id' => $userModel->id,
                'name' => $userModel->name,
                'surname' => $userModel->surname,
                'username' => $userModel->username,
                'email' => $userModel->email,
                'is_activated' => $userModel->is_activated,
            ];
        }
        // if no errors are encountered we can return a JWT
        return response()->json(compact('token', 'user'));
    });
    
    Route::post('auth/refresh', function (Request $request) {
        $token = Request::get('token');
        try {
            // attempt to refresh the JWT
            if (!$token = JWTAuth::refresh($token)) {
                return response()->json(['error' => 'could_not_refresh_token'], 401);
            }
        } catch (Exception $e) {
            // something went wrong
            return response()->json(['error' => 'could_not_refresh_token'], 500);
        }
        // if no errors are encountered we can return a new JWT
        return response()->json(compact('token'));
    });

    Route::post('auth/invalidate', function (Request $request) {
        $token = Request::get('token');
        try {
            // invalidate the token
            JWTAuth::invalidate($token);
        } catch (Exception $e) {
            // something went wrong
            return response()->json(['error' => 'could_not_invalidate_token'], 500);
        }
        // if no errors we can return a message to indicate that the token was invalidated
        return response()->json('token_invalidated');
    });

    Route::post('auth/signup', function () {
        $credentials = Input::only('email', 'password', 'password_confirmation');
        try {
            $userModel = UserModel::create($credentials);
            if ($userModel->methodExists('getAuthApiSignupAttributes')) {
                $user = $userModel->getAuthApiSignupAttributes();
            } else {
                $user = [
                    'id' => $userModel->id,
                    'name' => $userModel->name,
                    'surname' => $userModel->surname,
                    'username' => $userModel->username,
                    'email' => $userModel->email,
                    'is_activated' => $userModel->is_activated,
                ];
            }
        } catch (Exception $e) {
            return Response::json(['error' => $e->getMessage()], 401);
        }
        $token = JWTAuth::fromUser($userModel);
        return Response::json(compact('token', 'user'));
    });

















});