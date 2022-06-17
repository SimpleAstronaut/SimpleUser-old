<?php
header("Access-Control-Allow-Origin: *");

//GET请求函数
function sendSGHttp($Url, $Params, $timeout = 3, $Method = 'get')
{
    if (null == $Url) return null;
    $en_url = $Url . "?" . urldecode(urlencode("$Params"));

    $Curl = curl_init(); //初始化curl

    if ('get' == $Method) { //以GET方式发送请求
        //curl_setopt($Curl, CURLOPT_URL, "$Url?$Params");
        curl_setopt($Curl, CURLOPT_URL, $en_url);
    } else { //以POST方式发送请求
        curl_setopt($Curl, CURLOPT_URL, $Url);
        curl_setopt($Curl, CURLOPT_POST, 1); //post提交方式
        curl_setopt($Curl, CURLOPT_POSTFIELDS, $Params); //设置传送的参数
    }

    curl_setopt($Curl, CURLOPT_HEADER, false); //设置header
    curl_setopt($Curl, CURLOPT_RETURNTRANSFER, true); //要求结果为字符串且输出到屏幕上
    curl_setopt($Curl, CURLOPT_CONNECTTIMEOUT, $timeout); //设置等待时间

    $Res = curl_exec($Curl); //运行curl
    $Err = curl_error($Curl);

    if (false === $Res || !empty($Err)) {
        $Errno = curl_errno($Curl);
        $Info = curl_getinfo($Curl);
        curl_close($Curl);

        return array(
            'result' => false,
            'errno' => $Errno,
            'msg' => $Err,
            'info' => $Info,
        );
    }
    curl_close($Curl); //关闭curl
    return array(
        'result' => true,
        'msg' => $Res,
    );
}


$host = '127.0.0.1';
$user = '';
$password = '';
$dbName = '';
$link = new mysqli($host, $user, $password, $dbName);
if ($link->connect_error) {
    die("连接失败: " . $conn->connect_error);
}
$username = $_POST['username'];
$userPassWord = $_POST['password'];

//电子邮件验证
$email = $_POST['mail'];
$num = rand(10000, 99999);

$message = "target=" . $email . "&msg=" . $num;
$target = "";  //发送邮件接口，年久失修，已弃用

$mailreturn = sendSGHttp($target, $message);


//将验证码写入数据库
$AddMailNum = "INSERT INTO CAP ( USER_NAME, CAP, MAIL ) VALUES ('" . $username . "', '" . $num . "', '" . $email . "' )";
if ($link->query($AddMailNum) === TRUE) {
    echo "";
} else {
    echo "Error: " . $AddMailNum . "<br>" . $link->error;
}

$md5Password = md5($userPassWord);
$getUserName = "SELECT username FROM users";
$result = $link->query($getUserName);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $actUserName = $row['username'];
        if ($username == $actUserName) {
            echo '{"status":401}';
            exit();
        }
    }
    $time_now = time();
    $signUpUser = "INSERT INTO users (username, password, registration_date) VALUES ('" . $username . "', '" . $userPassWord . "' ," . $time_now . ")";
    if ($link->query($signUpUser) === TRUE) {
        $getuser_id = "SELECT user_id FROM users";
        $AnsGetUser_ID = $link->query($getuser_id);
        if ($AnsGetUser_ID->num_rows > 0) {
            while ($getUser_IDResult = $AnsGetUser_ID->fetch_assoc()) {
                $user_id = $getUser_IDResult['user_id'];
            }
            echo '{"status":201,"data":{ "username":"' . $username . '", "user_id":' . $user_id . '}}';
        } else {
            echo '{"status":220}';
        }
    } else {
        echo "Error: " . $signUpUser . "<br>" . $link->error;
    }
} else {
    echo "0 结果";
}
$link->close();
exit();
