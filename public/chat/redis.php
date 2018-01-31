<?php
/**
 * Created by PhpStorm.
 * User: linlinwang
 * Date: 2017/2/22 0022
 * Time: 20:59
 */
    header("content-type:text/html;charset=utf-8");

    ini_set('default_socket_timeout',-1);       //超时控制

    //实例化
    $redis = new Redis();
    //连接服务器
    $redis->pconnect('127.0.0.1',6379);
    //授权
    $redis->auth('');