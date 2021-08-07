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

// Route::get('/', function () {
//     return redirect('admin');
// });
Route::group([
	'namespace' => 'Frontend',
	'middleware' => ['web']
], function(){

    Route::get('/', 'NewsController@show')->name('web.home');

    Route::get('news/{type?}/{topic?}/{source?}/', 'NewsController@show')->name('web.news');

	Route::get('detail/{id}', 'NewsController@showDetail')->name('web.detail');
	
	Route::get('translate/{id}','NewsController@translate')->name('web.translate');

	//route user

	Route::group([
		'prefix' => 'user'
	], function() {
        Route::match(['get', 'post'], 'login', 'UserController@login')->name('web.user.login');

        Route::match(['get', 'post'], 'register', 'UserController@register')->name('web.user.register');

        Route::get('login/{provider}', 'UserController@redirect');

        Route::get('login/callback/{provider}','UserController@callback');
    
		Route::get('/logout', 'UserController@logout')->name('web.user.logout');

		Route::get('/profile', 'UserController@profile')->name('web.user.profile');

		Route::post('/update-profile', 'UserController@updateProfile')->name('web.user.update_profile');
	});

	Route::post('image-cropper/upload','UserController@upload');
});