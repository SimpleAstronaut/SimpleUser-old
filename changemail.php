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

$email = $_GET['email'];
$username = $_GET['username'];
$num = rand(10000, 99999);

$message = "target=" . $email . "&msg=" . $num;
$target = ""; //发送邮件接口，年久失修，已弃用

$mailreturn = sendSGHttp($target, $message);

$AddMailNum = "INSERT INTO CAP ( USER_NAME, CAP, MAIL ) VALUES ('" . $username . "', '" . $num . "', '" . $email . "' )";
if ($link->query($AddMailNum) === TRUE) {
    echo '{ "status":201 }';
} else {
    echo "Error: " . $AddMailNum . "<br>" . $link->error;
}
