<?php
header("Access-Control-Allow-Origin: *");
//获取操作参数
$token = $_GET['token'];
$cap = $_GET['cap'];

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
    $getCAP =  "SELECT USER_NAME, CAP, MAIL FROM CAP";
    $getCAPresult = $link->query($getCAP);
    while ($row = $getCAPresult->fetch_assoc()) {
        $CAP_in_sql = $row['CAP'];
        if ($CAP_in_sql == $cap) {
            $AddMail = "UPDATE users SET post='" . $row['MAIL'] . "' WHERE username='" . $username . "' ";
            mysqli_query($link, $AddMail);
            $return =  '{ "status":201, "email":"' . $row['MAIL'] . '"}';
        }
    }
} else {
    $link->close();
}

//关闭数据库和程序
echo $return;
exit();
