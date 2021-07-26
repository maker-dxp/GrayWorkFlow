<?php

/**
 * 响应头处理
 */

/* 错误码常量 */
const   OK                  =       200;
const   LOGIN_SUCCESS       =       2000;
const   REGISTER_SUCCESS    =       2001;

const   OPERATION_FAIL      =       300;

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

function sendHttpStatus(int $code) {
    static $_status = array(
        // Informational 1xx
        100 => 'Continue',
        101 => 'Switching Protocols',
        // Success 2xx
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        // Redirection 3xx
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Moved Temporarily ',  // 1.1
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        // 306 is deprecated but reserved
        307 => 'Temporary Redirect',
        // Client Error 4xx
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        // Server Error 5xx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        509 => 'Bandwidth Limit Exceeded'
    );
    if(isset($_status[$code])) {
        header('HTTP/1.1 '.$code.' '.$_status[$code]);
        // 确保FastCGI模式下正常
        header('Status:'.$code.' '.$_status[$code]);
    }
    return true;
}