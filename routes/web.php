<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/


//后台管理
Route::group(['middleware'=>'check.login'], function(){
    //后台注册
    Route::get('reg','Admins\LoginController@reg');
    //执行注册
    Route::post('reg','Admins\LoginController@doReg');

    //后台登录
    Route::get('login','Admins\LoginController@login');
    //执行登录
    Route::post('login','Admins\LoginController@doLogin');

    //验证码
    Route::get('captcha/{tmp}','Admins\LoginController@captcha');
});

Route::group(['middleware'=>'check.admin.login'], function(){
    //VPN管理
    //设置vpn
    Route::post('setVpn','Admins\TaskController@setVpn');

    //任务管理
    Route::get('/','Admins\TaskController@index');
    //投放任务
    Route::post('task/start/{id}','Admins\TaskController@doStart');
    //一键投放任务
    Route::post('task/startall','Admins\TaskController@doStartAll');
    //下线任务
    Route::post('task/pause/{id}','Admins\TaskController@doPause');
    //完成情况
    Route::get('task/record','Admins\TaskController@record');
    //任务记录详情
    Route::get('task/detail/{id}','Admins\TaskController@detail');

    Route::resource('task','Admins\TaskController');

    //账号管理
    //一键导入账号
    Route::post('account/import','Admins\AccountController@import');
    //清除该批次
    Route::post('account/clear','Admins\AccountController@clear');
    //清空全部账号
    Route::post('account/clearall','Admins\AccountController@clearall');
    //问题账号
    Route::get('account/issue','Admins\AccountController@issue');
    //问题账号修改为正常
    Route::post('account/change/{id}','Admins\AccountController@change');
    //问题账号全部修改为正常
    Route::post('account/changeall','Admins\AccountController@changeall');
    //问题账号全部删除
    Route::post('account/removeall','Admins\AccountController@removeall');
    //导出问题账号
    Route::get('account/issue/export','Admins\AccountController@issueExport');
    //重复账号
    Route::get('account/repeat','Admins\AccountController@repeat');
    //导出重复账号
    Route::get('account/repeat/export/{id}','Admins\AccountController@repeatExport');
    //批次情况
    Route::get('account/batch','Admins\AccountController@batch');
    //显示编辑批次
    Route::get('account/batch/{id}','Admins\AccountController@editBatch');
    //编辑批次
    Route::post('account/batch/{id}','Admins\AccountController@doEditBatch');

    Route::resource('account','Admins\AccountController');

    //设备管理
    Route::resource('client','Admins\ClientController');

    //报表管理
    Route::get('report','Admins\ReportController@index');
    //导出excel报表
    Route::get('report/export/{id}','Admins\ReportController@export');

    //管理员管理
    Route::resource('admin','Admins\AdminController');

    //退出后台
    Route::get('logout','Admins\LoginController@logout');
});

//客户端请求任务接口
//分发方式1 默认
Route::get('t/get','Client\TaskController@distributeTask');
//分发方式2 一个账号只绑定一个手机  一个账号 一天只刷一次
//Route::get('t/getByBind','Client\TaskController@distributeTaskByBind');

//客户端完成任务后接口
Route::post('t/finish','Client\TaskController@finishTask');

//获取vpn设置
Route::get('t/getVpn','Client\TaskController@getVpn');

//获取手机号码定位
Route::get('addr','Client\TaskController@getAddr');