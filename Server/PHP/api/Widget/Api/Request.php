<?php

class Widget_Api_Request {
    /**
     * 是否为GET
     *
     * @return bool
     */
    public static function isGet(): bool {
        return $_SERVER['REQUEST_METHOD'] == 'GET';
    }

    /**
     * 是否为POST
     *
     * @return bool
     */
    public static function isPost(): bool {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }

    /**
     * 是否为PUT
     *
     * @return bool
     */
    public static function isPut(): bool {
        return $_SERVER['REQUEST_METHOD'] == 'PUT';
    }

    /**
     * 是否为DELETE
     *
     * @return bool
     */
    public static function isDelete(): bool {
        return $_SERVER['REQUEST_METHOD'] == 'DELETE';
    }

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
}