<?php

class Widget_Api_Request {
    /**
     * 获取方法体
     *
     * @return false|string
     */
    public static function getRawData() {
        return file_get_contents('php://input');
    }

    /**
     * 解析方法体中的json数据
     *
     * @param $raw
     * @return false|array
     */
    public static function parseRawJson($raw = NULL) {
        if($raw === NULL){
            $ret = json_decode(self::getRawData(), true);
        }else {
            $ret = json_decode($raw, true);
        }

        if(!json_last_error() == JSON_ERROR_NONE){
            return false;
        } else {
            return $ret;
        }
    }

    /**
     * 获取token
     *
     * @return false|string
     */
    public static function getToken() {
        $header = getallheaders();

        if(!isset($header['Access-Token'])) {
            return false;
        }

        return $header['Access-Token'];
    }
}