<?php

$servername = "localhost";
$username = "123";
$password = "111";
$dbname = "GrayWorkFlow";

$conn = new mysqli($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error() . PHP_EOL);
}
$conn->query("set names utf8");

function addUser($conn,$user_name,$user_password,$user_qq){
    $insert_user = $conn->prepare("INSERT INTO user(user_name,user_password,user_qq) VALUES (?,?,?)");
    $insert_user -> bind_param("sss", $user_name, $user_password, $user_qq);
    $user_name = 'ttt测试';
    $user_password = '1212112aqa';
    $user_qq = '112346678';
    $result = $insert_user->execute();
    // $result = $conn->query('SELECT * FROM user');
    // var_dump($result->fetch_all(MYSQLI_ASSOC));
    !$result&&print($conn->error.PHP_EOL);
    $conn->close();
}

function getUserInfo($conn,$user_id){
    $get_user_info = $conn->prepare("SELECT * FROM user where user_id = ?");
    $get_user_info->bind_param("i",$user_id);
    $user_id = 0;
    $get_user_info->execute();
    $result = $get_user_info->get_result();
    // $result = $result->num_rows;
    !$result->num_rows&&print_r('1');
}

function verifyPassword($conn,$user_name,$user_password){
    $verify_password = $conn->prepare("SELECT user_id FROM user where user_name = ? and user_password = ?");
    $verify_password->bind_param('ss', $user_name,$user_password);
    $user_name='admin';
    $user_password='admin';
    $verify_password->execute();
    $result = $verify_password->get_result();
    print_r($result->num_rows);
    }


?>