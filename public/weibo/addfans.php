<?php
/**
 * Created by PhpStorm.
 * User: linlinwang
 * Date: 2017/2/24 0024
 * Time: 15:54
 */
    include 'redis.php';

    $id = $_GET['id'];
    $uid = $_GET['uid'];

    $redis->sAdd('user:'.$uid.':following', $id);       //sadd  键 值1 [值2…]  添加一个或多个元素到集合中
    $redis->sAdd('user:'.$id.':followers', $uid);

    header('location:list.php');