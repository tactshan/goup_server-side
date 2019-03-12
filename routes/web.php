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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

//接收微信服务器事件推送
Route::post('/weixin/valid1','Weixin\WeixinController@wxEvent');
//微信列表展示
Route::get('/weixin/wx_user_list','Weixin\WeixinController@wxUserList');
//给用户打标签
Route::get('/weixin/wx_user_tag/{openid}','Weixin\WeixinController@userTag');
Route::post('/weixin/wx_user_tag','Weixin\WeixinController@SendTag');
Route::get('/weixin/wx_set_tag','Weixin\WeixinController@setTag');
Route::get('/weixin/wx_get_tag','Weixin\WeixinController@getTag');
//设置黑名单
Route::get('/weixin/set_blank/{openid}','Weixin\WeixinController@blackList');

//Api测试
Route::get('/api/test','Api\ApiController@test');

