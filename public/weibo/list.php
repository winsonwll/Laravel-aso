<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>列表</title>
</head>
<body>
<?php
    include 'redis.php';
?>

<a href="add.php">注册</a>
<?php
    if(!empty($_COOKIE['auth'])){
        //get 键  获取一个键的值，返回值
        $uid = $redis->get('auth:'.$_COOKIE['auth']);
        //hget 键 字段  获取键中的一个指定字段的值
        $username = $redis->hGet('user:'.$uid, 'username');

        echo '欢迎您，'.$username.',<a href="logout.php">退出</a>';
    }else{
        echo '<a href="login.php">登录</a>';
    }

    //用户总数
    $count = $redis->lSize('uid');  //lsize,llen 键  获得队列的长度，如果不存在或空，则返回0；如果该键不是队列，则返回false
    //页大小
    $page_size = 3;
    //当前页码
    $page_num = isset($_GET['page']) ? $_GET['page'] : 1;
    //页总数
    $page_count = ceil($count/$page_size);

    //lrange 键 起始下标 终止下标    从队列中获取指定的返回值（从队列左边向右获取）
    /*下标：0代表队列中第一个元素，1代表第二个元素，依次类推
    -1代表队列中最后一个元素，-2代表倒数第二个元素*/
    $ids = $redis->lRange('uid', ($page_num-1)*$page_size, (($page_num-1)*$page_size+$page_size-1));

    foreach ($ids as $v){
        $data[] = $redis->hGetAll('user:'.$v);      //hgetall 键  获取键中的所有字段和值
    }
    if(empty($data)){
        die('暂无好友');
    }
?>

<table border="1">
    <tr>
        <th>uid</th>
        <th>username</th>
        <th>age</th>
        <th>操作</th>
    </tr>
    <?php foreach ($data as $v){?>
        <tr>
            <td><?php echo $v['uid'] ?></td>
            <td><?php echo $v['username'] ?></td>
            <td><?php echo $v['age'] ?></td>
            <td>
                <a href="del.php?id=<?php echo $v['uid'] ?>">删除</a>
                <a href="mod.php?id=<?php echo $v['uid'] ?>">编辑</a>

                <?php if(!empty($_COOKIE['auth']) && $uid != $v['uid']){
                    echo '<a href="addfans.php?id='.$v["uid"].'&uid='.$uid.'">加关注</a>';
                }?>
            </td>
        </tr>
    <?php } ?>
    <tr>
        <td colspan="4">
            <a href="?page=<?php echo (($page_num-1) <= 1) ? 1 : ($page_num-1) ?>">上一页</a>
            <a href="?page=<?php echo (($page_num+1) >= $page_count) ? $page_count : ($page_num+1) ?>">下一页</a>
            <a href="?page=1">首页</a>
            <a href="?page=<?php echo $page_count ?>">尾页</a>
            当前<?php echo $page_num ?>页
            总共<?php echo $page_count ?>页
            总共<?php echo $count ?>个用户
        </td>
    </tr>
</table>

<?php
    if(!empty($_COOKIE['auth'])){
?>
<table border="1">
    <caption>我关注了谁</caption>
    <?php
    //smembers 键  获取集合里面所有的元素
    $data = $redis->sMembers('user:'.$uid.':following');
    foreach ($data as $v){
        $row = $redis->hGetAll('user:'.$v);     //hgetall 键  获取键中的所有字段和值
        echo '<tr>';
        echo '<td>'.$row['uid'].'</td>';
        echo '<td>'.$row['username'].'</td>';
        echo '<td>'.$row['age'].'</td>';
        echo '</tr>';
    }
    ?>
</table>

<table border="1">
    <caption>我的粉丝</caption>
    <?php
        //smembers 键  获取集合里面所有的元素
        $data = $redis->sMembers('user:'.$uid.':followers');
        foreach ($data as $v) {
            $row = $redis->hGetAll('user:'.$v);     //hgetall 键  获取键中的所有字段和值
            echo '<tr>';
            echo '<td>'.$row['uid'].'</td>';
            echo '<td>'.$row['username'].'</td>';
            echo '<td>'.$row['age'].'</td>';
            echo '</tr>';
        }
    ?>
</table>
<?php
    }
?>
</body>
</html>