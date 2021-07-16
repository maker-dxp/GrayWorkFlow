<?php

$servername = "localhost";
$username = "";
$password = "";
$dbname = "GrayWorkFlow";

$conn = new mysqli($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error() . PHP_EOL);
}
$conn->query("set names utf8");

function ifUserExist($conn,$user_name){
    $user_if_exist = $conn->prepare("SELECT user_name FROM user where user_name = ?");
    // var_dump($user_if_exist);
    $user_if_exist->bind_parem("s",$user_name);
    $user_if_exist->excute();
    if(!($user_if_exist->get_result()->num_rows)){
        return TRUE;
    }
    return FALSE;
}

function addUser($conn,$user_name,$user_password,$user_qq){
    // if(ifUserExist($conn,$user_name)){
    //     return FALSE;
    // }
    $insert_user = $conn->prepare("INSERT INTO user(user_id,user_name,user_password,user_qq) SELECT (IFNULL(max(user_id),0) + 1),?,?,? from user;");
    // var_dump($insert_user);
    exit();
    $insert_user -> bind_param("sss", $user_name, $user_password, $user_qq);
    $result = $insert_user->execute();
    !$result&&print($conn->error.PHP_EOL);
    $id = $conn->query("select max(user_id) from user")->fetch_assoc()['max(user_id)'];
    $conn->close();
    return $id;
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
    $verify_password->execute();
    $result = $verify_password->get_result();
    if(!$result->num_rows){
        return FALSE;
    }
    $user_id = $result->fetch_assoc()['user_id'];
    return [TRUE,$user_id];
}

// addUser($conn,'1','1','1');

?>