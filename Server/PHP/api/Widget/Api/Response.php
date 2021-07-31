<?php

/* 错误码常量 */
const   OK                  =       200;
const   LOGIN_SUCCESS       =       2000;
const   REGISTER_SUCCESS    =       2001;

const   INVALID_REQUEST     =       400;
const   EMPTY_BODY          =       4000;
const   WRONG_JSON          =       4001;
const   EMPTY_BODY_FIELD    =       4002;
const   LOSE_SOME_INFO      =       4003;
const   WRONG_BODY          =       4004;

const   NOT_LOGGED_IN       =       401;
const   TOKEN_IS_NOT_FOUND  =       4010;
const   TOKEN_IS_EXPIRED    =       4011;
const   TOKEN_IS_INCORRECT  =       4012;
const   WRONG_USR_OR_PWD    =       4013;

const   OPERATION_FAIL      =       402;

const   PERMISSION_DENIED   =       403;

const   NOT_FOUND           =       404;
const   VIDEO_NOT_FOUND     =       4040;
const   USER_NOT_FOUND      =       4041;

const   FUNC_DENIED         =       405;

const   CONFLICT            =       409;
const   USER_EXIST          =       4090;
const   VIDEO_EXIST         =       4091;
const   JOB_EXIST           =       4092;

const   SERVER_ERROR        =       500;
const   DB_ERROR            =       5001;

class Widget_Api_Response {
    private const   MESSAGE     =       array(
        OK                      =>          '成功',
        INVALID_REQUEST         =>          '无效的请求',
        NOT_LOGGED_IN           =>          '你没有登录',
        TOKEN_IS_NOT_FOUND      =>          '没有这个Token',
        TOKEN_IS_EXPIRED        =>          'Token过期了',
        EMPTY_BODY              =>          'Body为空',
        WRONG_JSON              =>          'JSON格式有误',
        EMPTY_BODY_FIELD        =>          'Body字段缺失',
        FUNC_DENIED             =>          '方法不允许',
        CONFLICT                =>          '冲突',
        USER_EXIST              =>          '用户已存在',
        REGISTER_SUCCESS        =>          '注册成功',
        LOGIN_SUCCESS           =>          '登陆成功',
        TOKEN_IS_INCORRECT      =>          'Token不正确',
        SERVER_ERROR            =>          '服务器错误',
        DB_ERROR                =>          '数据库错误',
        PERMISSION_DENIED       =>          '权限不足',
        LOSE_SOME_INFO          =>          '缺少部分信息',
        NOT_FOUND               =>          '没有找到',
        VIDEO_NOT_FOUND         =>          '视频没有找到',
        VIDEO_EXIST             =>          '视频已经存在',
        JOB_EXIST               =>          '任务已经存在',
        WRONG_BODY              =>          '传入的信息有误',
        WRONG_USR_OR_PWD        =>          '用户名或密码错误',
        OPERATION_FAIL          =>          '操作失败',
        USER_NOT_FOUND          =>          '未找到用户'
    );

    /**
     * 发送数据
     *
     * @param int $code
     * @param array|null $data
     * @param string $message
     */
    public static function sendResponse(int $code, ?array $data = null, string $message = '') {
        $message = ($message) ?? self::MESSAGE[$code];

        $ret = array(
            'code' => $code,
            'message' => $message,
            'data' => $data
        );

        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST,GET,PUT');
        header('Content-type: application/json');
        echo json_encode($ret, JSON_UNESCAPED_UNICODE);
        exit();
    }

    /**
     * 发送http状态码
     *
     * @param int $code
     */
    public static function sendHttpStatus(int $code) {
        Zen_Response::setStatus($code);
    }
}