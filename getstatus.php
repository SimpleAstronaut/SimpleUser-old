<?php
header("Access-Control-Allow-Origin: *");

//获取请求信息
$token = $_GET['token'];

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
            }
        }
    }
} else {
    $ifPasstoken = 0;
    $return = "数据库错误";
}


//主程序
if ($ifPasstoken == 1) {

    //获取用户id
    $get_user_id = "SELECT username, user_id FROM users";
    $get_user_id_result = $link->query($get_user_id);
    while ($row = $get_user_id_result->fetch_assoc()) {
        $username_in_sql = $row['username'];
        if ($username == $username_in_sql) {
            $user_id = $row['user_id'];
        }
    }

    //获取用户状态信息
    $get_user_status = "SELECT USER_ID, STATE, CLASS , LICENCE FROM STATUS";
    $get_user_status_result = $link->query($get_user_status);
    while ($row = $get_user_status_result->fetch_assoc()) {
        $user_id_in_sql = $row['USER_ID'];
        if ($user_id == $user_id_in_sql) {
            $status = $row['STATE'];
            $class = $row['CLASS'];
            $licence = $row['LICENCE'];
            $return = '{ "status":200, "data":{ "user_id":' . $user_id . ',"user_state":' . $status . ',"user_class":' . $class . ',"licence":' . $licence . '}}';
        }
    }
} else {
    $link->close();
}


//关闭数据库和程序
echo $return;
exit();
