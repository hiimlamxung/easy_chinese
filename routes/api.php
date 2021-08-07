<?php

use Illuminate\Http\Request;

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

Route::group([
    'namespace' => 'Api'
], function(){
    // Route::group([
    //     'prefix' => 'currency'
    // ], function(){
    //     Route::post('/', 'MatchaController@currency');
    // });

    // news
    Route::group([
        'prefix' => 'news'
    ], function(){
        Route::get('/', 'NewsController@newsWithPage');
        Route::post('filter', 'NewsController@filterNews');
        Route::post('search', 'NewsController@searchNews');
    });

    Route::group([
        'prefix' => 'detail'
    ], function(){
        Route::get('{id}', 'NewsController@getDetailNews');
    });

     Route::group([
        'prefix' => 'translate'
    ], function(){
        Route::get('/', 'TranslateController@getListTrans');
        Route::post('/add-trans', 'TranslateController@addTrans');
        Route::post('/add-react', 'TranslateController@addReact');
    });

    // Sale off
    Route::group([
        'prefix' => 'sale'
    ], function(){
        Route::get('/', 'SaleController@getSaleoff');
    });

    // user
    Route::group([
        'prefix' => 'user'
    ], function(){
        Route::post('register', 'UserController@register');

        Route::post('login', 'UserController@loginWithEmail');

        Route::get('logout', 'UserController@logout');

        Route::post('change-password', 'UserController@changePassword');

        Route::get('profile', 'UserController@getProfile');

        Route::post('loginwithsocial', 'UserController@loginWithSocial');

        Route::post('login/apple', 'UserController@loginWithApple');

        Route::post('edit-image', 'UserController@editImage');

        Route::post('edit-profile', 'UserController@editProfile');
        
        Route::get('history/{language}', 'UserController@history');

        Route::get('premium', 'UserController@randomUsersPremium');
    });

    // Route::group([
    //     'prefix' => 'code'
    // ], function(){
    //     Route::post('active', 'CodeController@active');

    //     Route::get('/', 'CodeController@getCode');
    // });

    Route::group([
        'prefix' => 'premium'
    ], function(){
        Route::post('/active', 'PremiumController@active');

    });

     /**
     * Api for HSK test
     */
    Route::group([
        'prefix' => 'gethsk'
    ], function(){
        Route::get('/hsk/{hsk_id}', 'EHSKController@get');
        Route::get('/listhsk/{cate_id}', 'EHSKController@getListHSKWithCategory')->where(['cate_id' => '[0-9]+']);

        Route::post('/history', 'EHSKController@saveHistoryUser');
        Route::get('/history/{exam_id}', 'EHSKController@getHistoryUser');
        Route::get('/history-exam/{history_id}', 'EHSKController@getHistory');
        Route::get('/history-write/{exam_id}/{question_id}', 'EHSKController@getHistoryPartWrite');

    });

});