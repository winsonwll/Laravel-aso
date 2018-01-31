<?php
/**
 * Created by PhpStorm.
 * User: linlinwang
 * Date: 2017/2/24 0024
 * Time: 16:07
 */

    include 'redis.php';

    $redis->del('auth:'.$_COOKIE['auth']);
    setcookie('auth', '', time()-1);

    header('location:list.php');