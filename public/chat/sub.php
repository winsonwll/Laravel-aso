<?php
/**
 * Created by PhpStorm.
 * User: linlinwang
 * Date: 2017/2/23 0023
 * Time: 16:36
 */

    include './redis.php';

    $redis->subscribe(['tv1','tv2'],'callback');

    function callback($redis, $channel, $contect){
        /*echo '<pre>';
        var_dump(func_get_arg());*/

        echo $channel.' : '.$contect.'<br>';
        exit();
    }