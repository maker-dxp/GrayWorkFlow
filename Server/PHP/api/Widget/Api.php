<?php

abstract class Widget_Api extends Zen_Widget{
    /**
     * 发送数据
     *
     * @param int $code
     * @param string $message
     * @param array|null $data
     * <p>
     *  $data变量需要遵循以下规则：
     *      需要解析成js对象的以关联数组的形式(键值对)传递数组，需要解析成js数组时传递索引数组
     * </p>
     *
     * <p>
     * 该函数没有返回值，调用完毕后直接结束运行，请在处理完所有数据后调用该函数
     * </p>
     */
    protected static function sendResponse(int $code, ?array $data = null, string $message = '') {
        Widget_Api_Response::sendResponse($code, $data, $message);
    }

    /**
     * 设置http状态码
     *
     * @param int $code
     */
    protected static function sendHttpStatus(int $code) {
        Widget_Api_Response::sendHttpStatus($code);
    }

    /**
     * 检查登录
     *
     * @return Widget_User
     * @throws Widget_User_Exception
     * @throws Zen_DB_Exception
     */
    protected static function checkLogin() {
        return Widget_User::verify(Widget_Api_Request::getToken());
    }

    /**
     * 获取请求体
     *
     * @return array|false
     * <p>
     * 以数组的形式返回请求体
     * </p>
     */
    protected static function getRequestBody() {
        return Widget_Api_Request::parseRawJson();
    }
}