<?php
header("Access-Control-Allow-Origin: *");
$acckey = $_GET['acckey'];

//数据库连接
$servername = "localhost";
$sqlusername = "";
$sqlpassword = "";
$dbname = "";
$link = new mysqli($servername, $sqlusername, $sqlpassword, $dbname);
if ($link->connect_error) {
    die("连接失败,请联系管理人员:escscience@163.com并附上错误代码截图 " . $link->connect_error);
}


//向ACCKEY数据表查询ACCKEY数据
$sql = "SELECT acckey, time, username FROM ACCKEY";
$result = $link->query($sql);

//向users数据表查询user和password数据
$sqlusers = "SELECT username, password FROM users";
$UsersResult = $link->query($sqlusers);

if ($result->num_rows > 0) {
    // 输出数据
    while ($row = $result->fetch_assoc()) {
        $trueacckey = $row['acckey'];
        if ($trueacckey == $acckey) {
            $username = $row['username'];
            $time = $row['time'];
            while ($usersrow = $UsersResult->fetch_assoc()) {
                $password = $usersrow['password'];
                $trueUsername = $usersrow['username'];
                if ($username == $trueUsername) {
                    $TrueUserAccky = md5($username . $password . $time);
                    if ($TrueUserAccky == $acckey) {
                        $time_now = time();
                        $num = rand();
                        $TOKEN = md5($username . $password . $time_now . $num);
                        $return = '{"status":200,"token":"' . $TOKEN . '"}';
                        $newToken = "INSERT INTO TOKEN (TOKEN, TIME, USERNAME) VALUES ('" . $TOKEN . "', '" . $time_now . "', '" . $username . "')";
                        if ($link->query($newToken) === TRUE) {
                            echo $return;
                        } else {
                            echo "Error: " . $newToken . "<br>" . $link->error;
                        }
                    }
                }
            }
        }
    }
} else {
    echo "数据库错误";
}
$link->close();
exit();
