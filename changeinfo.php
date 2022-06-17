<?php
header("Access-Control-Allow-Origin: *");

//获取请求信息
$mode = $_POST['mode'];
$token = $_POST['token'];
$type = $_POST['type'];
$value = $_POST['value'];

//定义TOKEN状态变量
//$ifPasstoken = 0;

//定义返回信息
$return = "NULL";

//连接服务器
$servername = "localhost";
$sqlusername = "";
$sqlpassword = "";
$dbname = "";
$link = new mysqli($servername, $sqlusername, $sqlpassword, $dbname);

//验证token
$get_token = "SELECT TOKEN, TIME, USERNAME FROM TOKEN";
$get_token_result = $link->query($get_token);
if ($get_token_result->num_rows > 0) {
    while ($row = $get_token_result->fetch_assoc()) {
        $token_in_sql = $row['TOKEN'];
        if ($token_in_sql == $token) {
            $token_time = $row['TIME'];
            $time_now = time();
            if ($time_now - $token_time > 600) { //验证token是否过期
                $ifPasstoken = 0;
                $return = "token已过期";
            } else {
                $ifPasstoken = 1;
                $username = $row['USERNAME'];
                $return = "test";
            }
        }
    }
} else {
    $ifPasstoken = 0;
    $return = "数据库错误";
}


//获取用户id
$get_user_id = "SELECT username, user_id FROM users";
$get_user_id_result = $link->query($get_user_id);
if ($get_user_id_result->num_rows > 0) {
    while ($row = $get_user_id_result->fetch_assoc()) {
        $username_in_sql = $row['username'];
        if ($username_in_sql == $username) {
            $user_id = $row['user_id'];
        }
    }
} else {
    $return = "数据库错误1";
}


//主程序
if ($ifPasstoken == 1) {
    //获取操作类型
    switch ($mode) {
            //添加基本信息
        case "add":
            switch ($type) {
                    //添加头像
                case "avatar":
                    $changeinfo = "UPDATE users SET avatar='" . $value . "' WHERE username='" . $username . "'";
                    mysqli_query($link, $changeinfo);
                    $return = '{"status":201, "latestinfo":{ "avatar":"' . $value . '"}}';
                    break;

                    //添加游戏账户
                case "gameaccount":
                    $changeinfo = "UPDATE users SET gameacc='" . $value . "' WHERE username='" . $username . "'";
                    mysqli_query($link, $changeinfo);
                    $return = '{"status":201, "latestinfo":{ "gameaccount":"' . $value . '"}}';
                    break;

                default:
                    $return = '添加信息对象错误';
            }
            break;

            //修改基本信息
        case "change":
            switch ($type) {
                    //修改用户名
                case "username":
                    $changeinfo = "UPDATE users SET username='" . $value . "' WHERE user_id='" . $user_id . "'";
                    mysqli_query($link, $changeinfo);
                    $return = '{"status":201, "latestinfo":{ "username":"' . $value . '"}}';
                    break;

                    //修改密码
                case "password":
                    $changeinfo = "UPDATE users SET password='" . $value . "' WHERE user_id='" . $user_id . "'";
                    mysqli_query($link, $changeinfo);
                    $return = '{"status":201, "latestinfo":{ "password":"' . $value . '"}}';
                    break;

                    //修改头像
                case "avatar":
                    $changeinfo = "UPDATE users SET avatar='" . $value . "' WHERE username='" . $username . "'";
                    mysqli_query($link, $changeinfo);
                    $return = '{"status":201, "latestinfo":{ "avatar":"' . $value . '"}}';
                    break;

                    //修改游戏账户
                case "gameaccount":
                    $changeinfo = "UPDATE users SET gameacc='" . $value . "' WHERE username='" . $username . "'";
                    mysqli_query($link, $changeinfo);
                    $return = '{"status":201, "latestinfo":{ "gameaccount":"' . $value . '"}}';
                    break;

                default:
                    $return = "修改信息对象错误";
            }
            break;

            //删除用户信息(暂未开放)
        case "delete":
            $return = "删除用户功能暂未开放";
            break;

        default:
            $return = "功能操作错误";
            break;
    }
} else {
    $link->close();
}
echo $return;
exit();
