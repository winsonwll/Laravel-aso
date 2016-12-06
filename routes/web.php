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
    Route::get('/reg','Admins\LoginController@reg');
    //执行注册
    Route::post('/reg','Admins\LoginController@doReg');

    //后台登录
    Route::get('/login','Admins\LoginController@login');
    //执行登录
    Route::post('/login','Admins\LoginController@doLogin');

    //验证码
    Route::get('/captcha/{tmp}','Admins\LoginController@captcha');
});

Route::group(['middleware'=>'check.admin.login'], function(){
    //任务管理
    Route::get('/','Admins\TaskController@index');
    //投放任务
    Route::post('/task/start','Admins\TaskController@doStart');
    //下线任务
    Route::post('/task/pause','Admins\TaskController@doPause');
    //任务记录
    Route::get('/task/record','Admins\TaskController@record');

    Route::resource('/task','Admins\TaskController');

    //账号管理
    //一键导入账号
    Route::post('/account/import','Admins\AccountController@import');
    Route::resource('/account','Admins\AccountController');

    //设备管理
    Route::resource('/client','Admins\ClientController');

    //管理员管理
    Route::resource('/admin','Admins\AdminController');

    //退出后台
    Route::get('/logout','Admins\LoginController@logout');
});


//客户端请求任务接口
Route::get('/t/get','Client\TaskController@distributeTask');

//客户端完成任务后接口
Route::post('/t/finish','Client\TaskController@finishTask');