<?php

class Widget_Entry_User extends Widget_Api {
    /**
     * @map     '/api/user/login'
     * @method  POST
     */
    public static function login() {
        $data = self::getRequestBody();
        try{
            Widget_User::verify(array(
                $data['UserName'],
                $data['Password']
            ))->sendToken();
        }catch (Widget_User_Exception $e) {
            self::sendHttpStatus(401);
            self::sendResponse(NOT_LOGGED_IN);
        }
    }

    /**
     * @map     '/api/User/info'
     * @method  GET
     */
    public static function info() {
        $data = self::getRequestBody();
        try {
            $user_data = Widget_User::verify(
                $data['Access-Token']
            ); 
            self::sendResponse(200,array(
                "User" => $user_data -> nick_name,
                "Icon" => $user_data -> avatar,
                "Point" => $user_data -> point,
                "Permission" => $user_data -> authority,
                "lastLoginAt" => $user_data -> last_time
            ));
        }catch(Widget_User_Exception $e){
            self::sendHttpStatus(401);
            self::sendResponse(NOT_LOGGED_IN);
        }
    }

    /**
     * @map     '/api/User/pwd'
     * @method  POST
     */
    public static function pwd() {
        $data = self::getRequestBody();
        try {
            $user_data = Widget_User::verify(
                $data['Access-Token']
            );
            if($data["OriginPWD"] == $user_data -> user_password){
                $user_data -> user_password = $data["NewPWD"];
                self::sendResponse(200,null,"修改密码成功");
            }else{
                self::sendResponse(200,null,"修改密码失败");
            };
            
        }catch(Widget_User_Exception $e){
            self::sendHttpStatus(401);
            self::sendResponse(NOT_LOGGED_IN);
        }
    }

    /**
     * @map     '/api/User/name'
     * @method  POST
     */
    public static function name() {//是否存在命名冲突存在则改为Nickname
        $data = self::getRequestBody();
        try {
            $user_data = Widget_User::verify(
                $data['Access-Token']
            );
            $user_data -> user_password = $data["UserName"];
            self::sendResponse(200,null,"修改昵称成功");
        }catch(Widget_User_Exception $e){
            self::sendHttpStatus(401);
            self::sendResponse(NOT_LOGGED_IN);
        }
    }
}