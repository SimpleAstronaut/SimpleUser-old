<?php
header("Access-Control-Allow-Origin: *");
//获取操作参数
$mode = $_GET['mode'];

//连接服务器
$servername = "localhost";
$sqlusername = "";
$sqlpassword = "";
$dbname = "";
$link = new mysqli($servername, $sqlusername, $sqlpassword, $dbname);

//获取id接口
if ($mode == 'getid') {
    $username = $_GET['username'];
    $getUserid = "SELECT user_id, username FROM users";
    $result = $link->query($getUserid);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $user_id = $row['user_id'];
            $username_in_sql = $row['username'];
            if ($username == $username_in_sql) {
                echo '{"status":200, "username":"' . $username . '", "user_id":' . $user_id . '}';
            }
        }
    } else {
        echo 'null';
    }
} elseif ($mode == 'getinfo') {

    //获取请求数值
    $clientToken = $_GET['token'];
    $clientId = $_GET['user_id'];

    //验证token
    //获取TOKEN数据表数据
    $findToken = "SELECT TOKEN, TIME, USERNAME FROM TOKEN";
    $find_token_result = $link->query($findToken);

    if ($find_token_result->num_rows > 0) {
        while ($row = $find_token_result->fetch_assoc()) {
            $token_in_server = $row['TOKEN'];
            if ($token_in_server == $clientToken) {
                $token_time = $row['TIME'];
                $time_now = time();
                if ($time_now - $token_time > 600) { //验证token是否过期
                    echo 'token已过期';
                } else {
                    $username_in_token_server = $row['USERNAME'];

                    //在users数据表中查找用户信息
                    $find_info = "SELECT user_id, username, avatar, gameacc FROM users";
                    $find_info_result = $link->query($find_info);

                    //输出数据
                    if ($find_info_result->num_rows > 0) {
                        while ($row = $find_info_result->fetch_assoc()) {
                            $username_in_server = $row['username'];
                            if ($username_in_token_server == $username_in_server) {
                                $user_id_in_server = $row['user_id'];
                                $avatar = $row['avatar'];
                                if ($avatar == null) {
                                    $avatar = 'NULL';
                                } else {
                                    $avatar = $row['avatar'];
                                }
                                $gameacc = $row['gameacc'];
                                if ($gameacc == null) {
                                    $gameacc = 'NULL';
                                } else {
                                    $gameacc = $row['gameacc'];
                                }
                                $return = '{"status": 200,"data": {"username": "' . $username_in_server . '","data": {"username": "' . $username_in_server . '","user_id": ' . $user_id_in_server . ',"avatar": "' . $avatar . '","gameaccount":"' . $gameacc . '"}}}';
                                echo $return;
                            }
                        }
                    } else {
                        echo 'null';
                    }
                }
            }
        }
    } else {
        echo "token错误";
    }
}
$link->close();
exit();
