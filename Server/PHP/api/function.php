<?php

/**
 * 所有的功能入口文件
 */

include_once 'db.php';
include_once 'user.php';
include_once 'request.php';

/**
 * route    /api/user/info
 * method   GET/PUT/POST
 */
function userInfoEntry() {
    switch(true) {
        case isPut():
            createUser();
            break;
        case isGet():
            fetchUserInfo();
            break;
        case isPost():
            changeUserInfo();
            break;
        default:
            sendHttpStatus(403);
            sendHttpStatus(FUNC_DENIED);
    }
}

/**
 * route    /api/user/login
 * method   GET
 */
function userLoginEntry(){
    switch(true) {
        case isGet():
            login();
            break;
        default:
            sendHttpStatus(403);
            sendResponse(FUNC_DENIED);
    }
}

/**
 * route    /api/users/pwd
 * method   POST
 */
function userPwdEntry() {
    switch(true) {
        case isPost():
            changePassword();
            break;
        default:
            sendHttpStatus(403);
            sendHttpStatus(FUNC_DENIED);
    }
}

/**
 * route    /api/user/name
 * method   POST
 */
function userNameEntry() {
    switch(true) {
        case isPost():
            changeUserName();
            break;
        default:
            sendHttpStatus(403);
            sendHttpStatus(FUNC_DENIED);
    }
}

/**
 * route    /api/user/jobs
 * method   POST
 */
function userJobsEntry() {
    switch(true) {
        case isPost():
            changeUserJobs();
            break;
        default:
            sendHttpStatus(403);
            sendHttpStatus(FUNC_DENIED);
    }
}

/**
 * route    /api/video/info
 * method   GET/PUT
 */
function videoInfoEntry() {
    switch(true) {
        case isPut():
            createVideo();
            break;
        case isGet():
            getVideo();
            break;
        case isPost():
            changeVideo();
            break;
        default:
            sendHttpStatus(403);
            sendHttpStatus(FUNC_DENIED);
    }
}

/**
 * route    /api/video
 * method   GET/PUT
 */
function videoEntry() {
    switch(true) {
        default:
            sendHttpStatus(403);
            sendHttpStatus(FUNC_DENIED);
    }
}

/**
 * route    /api/task/info
 * method   GET/PUT
 */
function taskInfoEntry() {
    switch(true) {
        case isPut():
            createUser();
            break;
        case isGet():
            fetchUserInfo();
            break;
        case isPost():
            changeUserInfo();
            break;
        default:
            sendHttpStatus(403);
            sendHttpStatus(FUNC_DENIED);
    }
}

/**
 * route    /api/task
 * method   GET/PUT
 */
function taskEntry() {
    switch(true) {
        default:
            sendHttpStatus(403);
            sendHttpStatus(FUNC_DENIED);
    }
}