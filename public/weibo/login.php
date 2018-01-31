<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>登录</title>
</head>
<body>
<form action="" method="post">
    用户名：<input type="text" name="username"><br />
    密码：<input type="password" name="password"><br />
    <input type="submit" value="登录">
</form>

<?php
    include 'redis.php';

    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $pass = isset($_POST['password']) ? $_POST['password'] : '';

    //get 键                   获取一个键的值，返回值
    $uid = $redis->get('username:'.$username);
    if(!empty($uid)){
        //hget 键 字段         获取键中的一个指定字段的值
        $password = $redis->hGet('user:'.$uid, 'password');

        if(md5($pass) == $password){
            $auth = md5(time().$username.rand());
            //set 键 值       设置一个键和值，键存在则覆盖
            $redis->set('auth:'.$auth, $uid);       //保存uid，方便后续从cookie中取出
            setcookie('auth', $auth, time()+86400);
            
            header('location:list.php');
        }
    }
?>
</body>
</html>