<?php
/**
 * Created by PhpStorm.
 * User: linlinwang
 * Date: 2017/2/24 0024
 * Time: 9:45
 */

    include 'redis.php';

    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $age = $_POST['age'];

    //incr 键    指定键的值做加1操作，返回加后的结果（只能加数字）
    $uid = $redis->incr('userid');

    //rpush 键  值1 [值2…]             从队列右边向队列写入一个或多个值    第一个值就会写到队列的开头
    $redis->rPush('uid', $uid);

    //hmset 键  字段1 值1  字段2 值2    在一个键中，批量设置字段
    $redis->hMset('user:'.$uid, ['uid'=>$uid, 'username'=>$username, 'password'=>$password, 'age'=>$age]);

    //set 键  值                        设置一个键和值，键存在则覆盖
    $redis->set('username:'.$username, $uid);   //用户名和id绑定

    header('location:list.php');