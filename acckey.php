<?php
header("Access-Control-Allow-Origin: *");
$username = $_GET['username'];
$password = $_GET['password'];
$num = time();
$ACCKEY = md5($username . $password . $num);

/*echo '{"status":200,
"acckey":"'.$ACCKEY.'",
"time":'.$num.' }';*/
$servername = "localhost";
$sqlusername = "";
$sqlpassword = "";
$dbname = "";

$link = new mysqli($servername, $sqlusername, $sqlpassword, $dbname);
if ($link->connect_error) {
    die("连接失败,请联系管理人员:escscience@163.com并附上错误代码截图 " . $link->connect_error);
}

$NewAcckey = "INSERT INTO ACCKEY (acckey, time ,username) VALUES ('" . $ACCKEY . "', '" . $num . "' , '" . $username . "')";

if ($link->query($NewAcckey) === TRUE) {
    echo '{"status":201,"acckey":"' . $ACCKEY . '" }';
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
    echo $NewAcckey;
}
$link->close();
exit();
