<?php
/**
 * Created by PhpStorm.
 * User: linlinwang
 * Date: 2017/2/23 0023
 * Time: 17:50
 */

    header("content-type:text/html;charset=utf-8");

    //实例化
    $redis = new Redis();
    //连接服务器
    $redis->pconnect('127.0.0.1',6379);
    //授权
    $redis->auth('');