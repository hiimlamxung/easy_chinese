<?php

/*
|--------------------------------------------------------------------------
| Backend Routes
|--------------------------------------------------------------------------
|
| Here is where you can register Backend routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "Backend" middleware group. Enjoy building your Backend!
|
*/

Route::group([
    'namespace' => 'Backend'
], function(){
    /**
     * -----------------------
     * Before login
     * -----------------------
     */
    Route::group([
        'namespace' => 'Auth'
    ], function(){
        Route::get('/', 'LoginController@index')->name('backend.index');
        Route::post('login', 'LoginController@login')->name('backend.login');

        Route::get('register', 'RegisterController@index')->name('backend.register');
        Route::post('register', 'RegisterController@register')->name('backend.register.create');
    });

    /**
     * -----------------------
     * After login
     * -----------------------
     */
    Route::group([
        'middleware' => ['admin']
    ], function(){
        // Dashboard
        Route::group([
            'prefix' => 'dashboard'
        ], function(){
            Route::get('/', 'HomeController@index')->name('backend.dashboard');
            Route::get('logout', 'HomeController@logout')->name('backend.logout');
        });

        // Administrator
        Route::group([
            'prefix' => 'auth',
        ], function(){
            // Profile
            Route::group([
                'prefix' => 'profile',
                'namespace' => 'Admins'
            ], function(){
                Route::group([
                    'prefix' => '{admin}',
                    'where' => ['admin', '[0-9]+']
                ], function(){
                    Route::get('/', 'AdminController@profile')->name('backend.profile');
                    Route::put('image', 'AdminController@updateImage')->name('backend.profile.image');
                    Route::put('update', 'AdminController@updateProfile')->name('backend.profile.update');
                });
            });
        });

        //Route for user
        Route::group([
            'prefix' => 'user',
        ], function(){
            Route::get('/', 'Admins\AdminController@getAllAdmin')->name('admin.user');
            Route::get('/list-user', 'UserController@getAllUser')->name('admin.user.list');
            Route::post('/change-pass', 'UserController@changePass')->name('admin.users.change.pass');

            // Route for ajax
            Route::post('change-infor', 'Admins\AdminController@changeInfor')->name('admin.infor.change');
        
        });

        Route::group([
            'prefix' => 'news',
            'namespace' => 'News'
        ], function(){
            Route::get('/create', 'NewsController@index')->name('admin.news.create');
    
            Route::post('convert-pinyin', 'NewsController@convertPinyin')->name('admin.pinyin.convert');
        
            Route::post('create-news', 'NewsController@createNews')->name('admin.create.news');
    
            Route::get('manager/{module}', 'NewsController@newsManager')->name('admin.news.manager');

            Route::post('search', 'NewsController@search')->name('admin.news.search');

            Route::match(['get', 'post'], 'news-edit/{id}', 'NewsController@editNews')->name('admin.news.edit');

            Route::post('change-news-status', 'NewsController@changeStatus')->name('admin.news.change_status');


            // Route::post('convert-furi-edit', 'NewsController@toFuriEdit')->name('admin.Furi.Edit');
    
            // Route::post('change-news-order', 'NewsController@changeOrder')->name('admin.news.changeOrder');
            
            Route::post('pubDate', 'NewsController@pubDate')->name('admin.ajax.pubDate');
        });

        //Route for statistical
        Route::group([
            'prefix' => 'statistical'
        ], function(){
            Route::get('ctv', 'StatisticalController@collaborators')->name('admin.statistical.ctv');

            Route::get('censorship', 'StatisticalController@censorship')->name('admin.statistical.censorship');
        });

        //Route for comment
        Route::group([
            'prefix' => 'comment'
        ], function(){
            Route::post('send', 'CommentController@addComment')->name('admin.comment.add');
        });

        //Route for sale
        Route::group([
            'prefix' => 'sale'
        ], function(){
            Route::match(['get', 'post'], '/', 'SaleController@getcountry')->name('admin.saleoff');

            Route::post('edit', 'SaleController@editCountry')->name('admin.saleoff.edit');

            Route::post('change-status', 'SaleController@changeStatus')->name('admin.saleoff.change');

            Route::post('change-all', 'SaleController@changeAll')->name('admin.saleoff.changeAll');
        });

        //Code
        Route::group([
            'prefix' => 'code'
        ], function(){
            Route::get('/', 'CodeController@show')->name('admin.code.list');
            Route::get('generate', 'CodeController@generate')->name('admin.code.generate');
            Route::get('create', 'CodeController@create')->name('admin.code.create');
            Route::match(['get', 'post'], 'pick', 'CodeController@pick')->name('admin.code.pick');
            Route::post('active', 'CodeController@active')->name('admin.premium.active');
        });

    });
});