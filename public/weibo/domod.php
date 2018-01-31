<?php
/**
 * Created by PhpStorm.
 * User: linlinwang
 * Date: 2017/2/24 0024
 * Time: 16:02
 */

    include 'redis.php';

    $uid = $_POST['uid'];
    $username = $_POST['username'];
    $age = $_POST['age'];

    //hmset 键  字段1 值1  字段2  值2    在一个键中，批量设置字段
    $res = $redis->hMset('user:'.$uid, ['username'=>$username, 'age'=>$age]);
    if($res){
        header('location:list.php');
    }else{
        header('location:mod.php?id='.$uid);
    }