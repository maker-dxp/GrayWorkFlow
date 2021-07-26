<?php

/**
 * 请求头处理
 */

function isGet(): bool {
    return $_SERVER['REQUEST_METHOD'] == 'GET';
}

function isPost(): bool {
    return $_SERVER['REQUEST_METHOD'] == 'POST';
}

function isPut(): bool {
    return $_SERVER['REQUEST_METHOD'] == 'PUT';
}

function isDelete(): bool {
    return $_SERVER['REQUEST_METHOD'] == 'DELETE';
}

function getRawData() {
    return file_get_contents('php://input');
}

/**
 * @param null $raw
 * @return false|array
 */
function parseRawJson($raw = NULL) {
    if($raw === NULL){
        $ret = json_decode(getRawData(), true);
    }else {
        $ret = json_decode($raw, true);
    }

    if(!json_last_error() == JSON_ERROR_NONE){
        return false;
    } else {
        return $ret;
    }
}

/** 从 Header Authorization 中获取 Token */
function getBearerToken(){
    $headers = getAuthorizationHeader();
    if (!empty($headers)) {
        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }
    }
    return null;
}

/** 获取 Header Authorization 中的数据 */
function getAuthorizationHeader(){
    $headers = null;
    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER["Authorization"]);
    } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    } elseif (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
        $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
        if (isset($requestHeaders['Authorization'])) {
            $headers = trim($requestHeaders['Authorization']);
        }
    }
    return $headers;
}

/** 获取请求头中的token */
function getToken() {
    $header = getallheaders();

    if(!isset($header['Access-Token'])) {
        return false;
    }

    return $header['Access-Token'];
}