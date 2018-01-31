<?php
/**
 * Created by PhpStorm.
 * User: linlinwang
 * Date: 2017/2/23 0023
 * Time: 16:54
 */
    include './redis.php';

    $redis->publish('tv1',$_POST['content']);