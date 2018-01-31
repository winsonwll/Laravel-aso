<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>修改</title>
</head>
<body>
    <form action="domod.php" method="post">
        <input type="hidden" value="<?php echo $data['uid']?>" name="uid">
        用户名：<input type="text" name="username" value="<?php echo $data['username']?>"><br>
        年龄：<input type="text" name="age" value="<?php echo $data['age']?>"><br>
        <input type="submit" value="修改">
        <input type="reset" value="重新填写">
    </form>

    <?php
        include 'redis.php';
    
        $uid = $_GET['id'];
        $data = $redis->hGetAll('user:'.$uid);      //hgetall 键  获取键中的所有字段和值
    ?>
</body>
</html>