<?php

include_once 'response.php';

//路由表常量
const FILE  =   0;
const FUNC  =   1;

//路由表
/*
 * 路由表写法：
 *  [路由] => array( 0=>[文件名], 1=>[函数] )
 */
const ROUTE_MAP = array(
    '/api/users/login' => ['auth.php', 'doLogin'],
    '/api/users/createUser' => ['auth.php', 'doCreateUser'],
    '/api/works/info' => ['function.php', 'workInfo']
//    '/api/test' => ['index.php', 'test']         //测试代码
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

    return $requestUri;
}

function route() {
    $fullPath = getRequestUri();
    if($pos = strpos($fullPath, '?')) {
        $path = substr($fullPath, 0, $pos);
    }else {
        $path = $fullPath;
    }

    if(!isset(ROUTE_MAP[$path]) && !file_exists(ROUTE_MAP[$path][FILE])) {
        sendHttpStatus(400);
        sendResponse(FUNC_DENIED);
    }

    include_once ROUTE_MAP[$path][FILE];
    call_user_func(ROUTE_MAP[$path][FUNC]);
}
//测试代码
//function test() {
//    $code = (int)$_GET['code'];
//    sendResponse($code);
//}

//开始路由
route();