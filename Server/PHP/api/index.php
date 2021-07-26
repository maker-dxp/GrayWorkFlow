<?php

/**
 * 程序入口
 */

require_once 'config.php';

//路由表常量 - 暂时不用 保留
const FILE  =   0;
const FUNC  =   1;

//路由表 - 所有的函数都位于function.php
/*
 * 路由表写法：
 *  [路由] => [函数]
 */
const ROUTE_MAP = array(
    '/api/'                  =>      'helloWorld',
    '/api/user/login'       =>      'userLoginEntry',
    '/api/user/info'        =>      'userInfoEntry',
    '/api/user/pwd'         =>      'userPwdEntry',
    '/api/user/name'        =>      'userNameEntry',
    '/api/user/jobs'        =>      'userJobsEntry',
    '/api/video/info'       =>      'videoInfoEntry',
    '/api/video'            =>      'videoEntry',
    '/api/task/info'        =>      'taskInfoEntry',
    '/api/task'             =>      'taskEntry'
);

function getRequestUri(): string {
    //处理requestUri
    $requestUri = '/';

    if (isset($_SERVER['HTTP_X_REWRITE_URL'])) { // check this first so IIS will catch
        $requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
    } elseif (
        // IIS7 with URL Rewrite: make sure we get the unencoded url (double slash problem)
        isset($_SERVER['IIS_WasUrlRewritten'])
        && $_SERVER['IIS_WasUrlRewritten'] == '1'
        && isset($_SERVER['UNENCODED_URL'])
        && $_SERVER['UNENCODED_URL'] != ''
    ) {
        $requestUri = $_SERVER['UNENCODED_URL'];
    } elseif (isset($_SERVER['REQUEST_URI'])) {
        $requestUri = $_SERVER['REQUEST_URI'];
        $parts       = @parse_url($requestUri);

        if (isset($_SERVER['HTTP_HOST']) && strstr($requestUri, $_SERVER['HTTP_HOST'])) {
            if (false !== $parts) {
                $requestUri  = (empty($parts['path']) ? '' : $parts['path'])
                    . ((empty($parts['query'])) ? '' : '?' . $parts['query']);
            }
        } elseif (!empty($_SERVER['QUERY_STRING']) && empty($parts['query'])) {
            // fix query missing
            $requestUri .= '?' . $_SERVER['QUERY_STRING'];
        }
    } elseif (isset($_SERVER['ORIG_PATH_INFO'])) { // IIS 5.0, PHP as CGI
        $requestUri = $_SERVER['ORIG_PATH_INFO'];
        if (!empty($_SERVER['QUERY_STRING'])) {
            $requestUri .= '?' . $_SERVER['QUERY_STRING'];
        }
    }
    if(!empty(WWWROOT)){
        $requestUri = substr($requestUri,strlen(WWWROOT)+1);
    }
    if(empty(APIROOT)){
        $requestUri = '/api'.$requestUri;
    }
    return $requestUri;
}

function route() {
    include_once 'function.php';
    $fullPath = getRequestUri();
    if ($pos = strpos($fullPath, '?')) {
        $path = substr($fullPath, 0, $pos);
    } else {
        $path = $fullPath;
    }

    if (!isset(ROUTE_MAP[$path])) {
        sendHttpStatus(400);
        sendResponse(INVALID_REQUEST);
    }

    call_user_func(ROUTE_MAP[$path]);
}

function exceptionHandle($exception) {
    include_once 'response.php';

    sendHttpStatus(500);
    sendResponse(SERVER_ERROR);
}

/** 设置异常和错误处理 */
set_exception_handler('exceptionHandle');
set_error_handler('exceptionHandle',E_USER_ERROR);

route();
