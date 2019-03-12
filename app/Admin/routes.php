<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');

    //微信用户列表
    $router->get('/admin/wx_users', 'WeixinController@index');
    //微信群发
    $router->get('/admin/group_send', 'WeixinController@wx_group_send_view');
    $router->post('/admin/action/group_send','WeixinController@wx_group_send');

    //获取微信access_token
    $router->get('/admin/get_access_token','WeixinController@getAccessToken');
    //微信已定义菜单
    $router->get('/weiixn/custom_menu','WeixinController@customMenu');




    //月考
    //接受微信的推送事件
    $router->get('/month/get_wx_event','WeixinController@wxEvent');
    //用户列表展示
    $router->get('/month/wx_users','WeixinController@mon_user_list');

});



