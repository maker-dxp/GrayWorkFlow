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
}