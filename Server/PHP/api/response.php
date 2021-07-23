<?php

include_once 'function.php';

/* 错误码常量 */
const   OK                  =       200;
const   LOGIN_SUCCESS       =       2000;
const   REGISTER_SUCCESS    =       2001;

const   INVALID_REQUEST     =       400;
const   EMPTY_BODY          =       4000;
const   WRONG_JSON          =       4001;
const   EMPTY_BODY_FIELD    =       4002;

const   NOT_LOGGED_IN       =       401;
const   TOKEN_IS_NOT_FOUND  =       4010;
const   TOKEN_IS_EXPIRED    =       4011;
const   TOKEN_IS_INCORRECT  =       4012;

const   FUNC_DENIED         =       405;

const   CONFLICT            =       409;
const   USER_EXIST          =       4090;

const   SERVER_ERROR        =       500;
const   DB_ERROR            =       5001;


/* 错误码信息数组 */
const   MESSAGE             =       array(
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
    DB_ERROR                =>          '数据库错误'
);

/**
 * 发送数据
 *
 * @param int $code
 * @param string|null $message
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
function sendResponse(int $code, array $data = NULL, string $message = NULL) {
    $message = ($message) ?? MESSAGE[$code];

    $ret = array(
      'code' => $code,
      'message' => $message,
      'data' => $data
    );

    header('Content-type: application/json');
    echo json_encode($ret, JSON_UNESCAPED_UNICODE);
    exit();
}