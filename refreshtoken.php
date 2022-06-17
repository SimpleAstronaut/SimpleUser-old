<?php
header("Access-Control-Allow-Origin: *");
$old_token = $_POST['token'];

$servername = "localhost";
$sqlusername = "";
$sqlpassword = "";
$dbname = "";
$link = new mysqli($servername, $sqlusername, $sqlpassword, $dbname);
if ($link->connect_error) {
    die("连接失败,请联系管理人员:escscience@163.com并附上错误代码截图 " . $link->connect_error);
}

$con = mysqli_connect("localhost", "simple_users", "kG3NLSms3KHe4wWz", "simple_users");
if (mysqli_connect_errno()) {
    echo "连接失败: " . mysqli_connect_error();
}


$sqltoken = "SELECT TOKEN, TIME FROM TOKEN";
$result = $link->query($sqltoken);
$time_now = time();
$time_accp = 600;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $token_in_sql = $row['TOKEN'];
        $time_in_sql = $row['TIME'];
        if ($old_token == $token_in_sql) {
            $time_differerce = $time_now - $time_in_sql;
            if ($time_now - $time_in_sql > "600") {
                echo 'token已过期';
            } else {
                $token_text = '"' . $token_in_sql . '"';
                $token_time_update = "UPDATE TOKEN SET TIME=" . $time_now . " WHERE TOKEN=" . $token_text;
                $revaval = mysqli_query($con, $token_time_update);
                echo '{"status":201,"token":"' . $token_in_sql . '","time_out":600 }';
            }
        }
    }
} else {
    echo "数据库错误";
}
$link->close();
exit();
