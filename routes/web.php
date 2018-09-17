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

Route::get('/', 'PagesController@root')->name('root');

Auth::routes();

//需要把这个路由放在 auth 这个中间件的路由组里面，因为只有已经登录的用户才能看到这个提示界面。
Route::group(['middleware' => 'auth'],function(){
	Route::get('/email_verify_notice','PagesController@emailVerifyNotice')->name('email_verify_notice');
	Route::group(['middleware' => 'email_verified'],function(){
		Route::get('/test',function(){
			return "Your email is verified";
		});
	});
});
