<?php

class Widget_Entry_User extends Widget_Api {
    /**
     * @map     '/api/User/login'
     * @method  POST
     */
    public static function login() {
        $data = self::getRequestBody();
        try {
            Widget_Users::verify(array(
                'user_name'     =>  $data['UserName'],
                'user_passwd'   =>  $data['Password']
            ))->sendToken();
        } catch (Widget_Users_Exception $e) {
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
        if(self::checkLoginFromToken()){
            $user = Widget_Users::factory($data['uid']);
            self::sendResponse(200,array(
                "User"          => $user->nick_name,
                "Icon"          => $user->avatar,
                "Point"         => $user->point,
                "Permission"    => $user->authority,
                "lastLoginAt"   => $user->last_time
            ));
        }else{
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
            $user_data = Widget_Users::verify(
                $data['Access-Token']
            );
            if($data["OriginPWD"] == $user_data -> user_password){
                $user_data -> user_password = $data["NewPWD"];
                $user_data -> save();
                self::sendResponse(200,null,"修改密码成功");
            }else{
                self::sendResponse(200,null,"修改密码失败");
            };
            
        }catch(Widget_Users_Exception $e){
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
            $user = Widget_Users::verify(Widget_Api_Request::getToken());
            $user -> user_name = $data["UserName"];
            $user -> save();
            self::sendResponse(200,array('NewName' => $data["UserName"]),"修改昵称成功");
        }catch(Widget_Users_Exception $e){
            self::sendHttpStatus(401);
            self::sendResponse(NOT_LOGGED_IN);
        }
    }
}