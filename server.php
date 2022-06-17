<?php
//该接口返回用户基本信息数据

header("Access-Control-Allow-Origin: *");
//header('content-type:application/json;charset=utf8');
$user_id = $_GET['user_id'];
$host = '127.0.0.1';
$user = '';
$password = '';
$dbName = '';
$link = new mysqli($host, $user, $password, $dbName);
if ($link->connect_error) {
    die("400: " . $conn->connect_error);
}
$sql = "SELECT * FROM users where user_id = '" . $user_id . "'";
$result = $link->query($sql);
if ($result->num_rows > 0) {
    // 输出数据
    while ($row = $result->fetch_assoc()) {
        $username = $row["username"];
        $user_id = $row["user_id"];
        $data = $row["registration_date"];
        $avatar = $row["avatar"];
    }
    $return = '{"status":200,"data":[{"username": ' . '"' . $username . '"' . ',"data": {"username": ' . '"' . $username . '"' . ',"user_id": ' . '"' . $user_id . '"' . ',"gameaccount":"SimpleAstronaut"}
        }
    ]
}';
    echo $return;
    $link->close();
} else {
    echo "400";
    $link->close();
}
