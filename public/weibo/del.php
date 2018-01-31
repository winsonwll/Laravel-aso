<?php
/**
 * Created by PhpStorm.
 * User: linlinwang
 * Date: 2017/2/24 0024
 * Time: 16:05
 */

    include 'redis.php';

    $uid = $_GET['id'];
    $redis->del('user:'.$uid);      //del 键  删除一个键(key) 操作key的方法  所有的redis类型都可以使用del
    $redis->lRem('uid', $uid);       //lrem  键  n  指定值
    /*从队列中删除n个值为“指定值”的元素
	n > 0 	从队列头向尾删除n个元素
	n < 0 	从队列尾向头删除n个元素
	n = 0	删除所有值为“指定值”的元素*/

    header('location:list.php');