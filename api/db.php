<?php

$servername = "localhost";
$dbusername = "";
$dbpassword = "";
$dbname = "GrayWorkFlow";

$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error() . PHP_EOL);
}
$conn->query("set names utf8");

function ifUserExist($conn, string $user_name){
    /** @var mysqli_stmt $user_if_exist */
    $user_if_exist = $conn->prepare("SELECT `user_name` FROM `user` where `user_name` = ?");
    $user_if_exist->bind_param("s",$user_name);
    $user_if_exist->execute();

    /** @var mysqli_result $result */
    $result = $user_if_exist->get_result();

    if($result->num_rows){
        return TRUE;
    }
    return FALSE;
}

function addUser($conn, string $user_name, string $user_password, string $user_qq){
    if (ifUserExist($conn, $user_name)) {
        return FALSE;
    }
    /** @var mysqli_stmt $insert_user */
    $insert_user = $conn->prepare("INSERT INTO `user`(`user_id`, `user_name`, `user_password`, `user_qq`) SELECT (IFNULL(max(user_id), 0) + 1), ?, ?, ? FROM `user`;");
    $insert_user->bind_param("sss", $user_name, $user_password, $user_qq);
    $insert_user->execute();
    $id = $conn->query("SELECT max(user_id) FROM `user`")->fetch_assoc()['max(user_id)'];
    $conn->close();
    return $id;
}

function getUserInfo($conn, int $user_id){
    /** @var mysqli_stmt $get_user_info */
    $get_user_info = $conn->prepare("SELECT * FROM `user` where `user_id` = ?;");
    $get_user_info->bind_param("i",$user_id);
    $get_user_info->execute();
    /** @var mysqli_result $result */
    $result = $get_user_info->get_result();
    if(!$result) { return false; }
    return true;
}

function verifyPassword($conn, string $user_name, string $user_password){
    /** @var mysqli_stmt $verify_password */
    $verify_password = $conn->prepare("SELECT `user_id` FROM `user` WHERE `user_name` = ? AND `user_password` = ?;");
    $verify_password->bind_param('ss', $user_name,$user_password);
    $verify_password->execute();
    /** @var mysqli_result $result */
    $result = $verify_password->get_result();
    if(!$result->num_rows){
        return FALSE;
    }
    $user_id = $result->fetch_assoc()['user_id'];
    return [TRUE,$user_id];
}


?>