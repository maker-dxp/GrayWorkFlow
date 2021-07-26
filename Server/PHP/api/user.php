<?php

/**
 * 处理登录验证
 */

use Firebase\JWT\JWT;
include_once 'Firebase/JWT/JWT.php';
include_once 'Firebase/JWT/SignatureInvalidException.php';
include_once 'Firebase/JWT/BeforeValidException.php';
include_once 'Firebase/JWT/ExpiredException.php';
include_once 'response.php';
include_once 'db.php';
include_once 'work.php';

//密钥
const KEY = 'M8sjfLyfVmUDUPmq';

//默认头像位置
const AVATAR = '';

const JOB_TABLE = array(
    'su',
    'admin',
    'trans_maker',
    'trans_proofreader',
    'axis_maker',
    'axis_proofreader',
    'effect_maker',
    'video_maker'
);

function createToken($username, $userid, $usergroup='Undefined'): string {
    $key = '344'; //key，唯一标识
    $time = time(); //当前时间
    $token = [
        'iat' => $time, //签发时间
        'nbf' => $time , //(Not Before)：某个时间点后才能访问，比如设置time+30，表示当前时间30秒后才能使用
        'exp' => $time+7200, //过期时间,这里设置2个小时
        'data' => [ //自定义信息，不要定义敏感信息
            'username' => $username,
            'uid' => $userid,
            'jobs' => $usergroup
        ]
    ];

    //签发token
    return JWT::encode($token, $key,'HS256');
}

// $token：签发的token
function verifyToken(string $token) {
    //验证 JWT
    $key = '344';
    try {
        JWT::$leeway = 60;  //允许的时间误差,单位:min
        $decoded = JWT::decode($token, $key, ['HS256']);
        return $decoded->data;
    } catch(Exception $e) {  //捕获所有异常
        sendHttpStatus(401);
        sendResponse(TOKEN_IS_INCORRECT);
        return false;
    }
}

function verifyPassword(string $user_name, string $user_password) {
    $result = DB::get()
        ->prepare("SELECT `uid` FROM `users` WHERE `user_name` = :uname AND `user_password` = :upasswd;")
        ->execute(array(
            'uname' => $user_name,
            'upasswd' => hashPassword($user_password)
        ))->fetch(PDO::FETCH_ASSOC);

    if(!$result){
        return false;
    }

    return $result['uid'];
}

function getUserInfo(int $user_id) {
    $result = DB::get()
        ->prepare("SELECT * FROM `users` where `uid` = :uid;")
        ->execute(array(
            'uid' => $user_id
        ))->fetch(PDO::FETCH_ASSOC);

    if(!$result) { return false; }

    return $result;
}

function isLogin(): bool {
    return (bool)verifyToken(getToken());
}

function getRandName() {
    return 'user_' . substr(md5(time()), 0, 8);
}

function getRandPassword() {
    return substr(hash_hmac('sha256', getRandName(), KEY), 0, 16);
}

function hashPassword(string $password) {
    return hash_hmac('sha256', "Gray" . $password . "Wind", KEY);
}

/**
 * 处理用户登录
 * 设置token，返回登录信息
 *
 * route    /users/login
 * method   POST
 */
function login() {
    if(!($raw = getRawData())) {
        sendHttpStatus(400);
        sendResponse(EMPTY_BODY);
    }

    if(!($req = parseRawJson($raw))) {
        sendHttpStatus(400);
        sendResponse(WRONG_JSON);
    }

    if(isset($req['UserName']) && isset($req['Password'])){
        $username = $req['UserName'];
        $password = $req['Password'];

        if(!($uid = verifyPassword($username, $password))) {
            sendHttpStatus(401);
            sendResponse(WRONG_USR_OR_PWD);
        }

        //获取信息
        $info = DB::get()
            ->prepare("SELECT * FROM `users` WHERE `uid` = :uid;")
            ->execute(array(
                'uid' => $uid
            ))->fetch(PDO::FETCH_ASSOC);
        //更新最后登陆时间
        DB::get()
            ->prepare("UPDATE `users` SET `last_time` = :ltime WHERE `uid` = :uid;")
            ->execute(array(
                'ltime' => date("Y-m-d H:i:s"),
                'uid' => $uid
            ));

        $token = createToken($username, $uid);

        $data = array(
            'UserName' => $info['user_name'],
            'Icon' => $info['avatar'],
            'Point' => $info['point'],
            'Jobs' => (unserialize($info['jobs']) === false) ? NULL : unserialize($info['jobs']),
            'Access-Token' => $token,
            'lastLoginAt' => $info['last_time'],
        );

        sendResponse(OK, $data);
    } else {
        sendHttpStatus(400);
        sendResponse(INVALID_REQUEST);
    }
}

/**
 * 处理用户注册
 * 必须先登录且有admin权限
 * 返回注册了的用户名、uid、密码
 *
 * route    /api/users/register
 * method   POST
 */
function createUser(){
    if(!(isLogin())) {
        sendHttpStatus(403);
        sendResponse(NOT_LOGGED_IN);
    }
    $token = verifyToken(getToken());

    if(!($raw = getRawData())) {
        sendHttpStatus(400);
        sendResponse(EMPTY_BODY);
    }

    if(!($req = parseRawJson($raw))) {
        sendHttpStatus(400);
        sendResponse(WRONG_JSON);
    }

    //鉴权
    $uid = $token->uid;
    $auth = unserialize(DB::get()
        ->prepare("SELECT `jobs` FROM `users` WHERE `uid` = :uid;")
        ->execute(array(
            'uid' => $uid
        ))->fetch()[0]);

    if(array_search('admin', $auth) === false &&
        array_search('su', $auth) === false
    ){
        sendHttpStatus(403);
        sendResponse(PERMISSION_DENIED);
    }

    //检查是否已经注册
    $user_name = $req['UserName'];
    if(empty($user_name)){
        sendHttpStatus(400);
        sendResponse(EMPTY_BODY_FIELD);
    }
    $password = !empty($req['Password']) ? $req['Password'] : getRandPassword();
    $display_name = !empty($req['DisplayName']) ? $req['DisplayName'] : getRandName();
    $uid = DB::get()
        ->prepare("SELECT `uid` FROM `users` WHERE `user_name` = :uname;")
        ->execute(array(
            'uname' => $user_name
        ))->fetch();

    if(!empty($uid)) {
        sendHttpStatus(409);
        sendResponse(USER_EXIST);
    }

    $ret = DB::get()
        ->prepare("
        INSERT INTO `users`(
            `uid`,
            `user_name`,
            `user_password`,
            `display_name`,
            `avatar`,
            `jobs`,
            `point`,
            `create_time`
        )
        SELECT 
                IFNULL(MAX(`uid`), 0) + 1,
                :user_name,
                :user_password,
                :display_name,
                :avatar,
                :permission,
                0,
                :ctime
        FROM `users`;")
        ->execute(array(
            'user_name' => $user_name,
            'user_password' => hashPassword($password),
            'display_name' => $display_name,
            'avatar' => $req['Icon'] ?? AVATAR,
            'permission' => serialize(array()),
            'ctime' => date("Y-m-d")
        ));

    if($ret){
        $data = [
            'UserName' => $user_name,
            'Password' => $password,
            'DisplayName' => $display_name];
        sendResponse(REGISTER_SUCCESS, $data);
    } else {
        sendHttpStatus(400);
        sendResponse(OPERATION_FAIL);
    }
}

function fetchUserInfo() {
    if(!(isLogin())) {
        sendHttpStatus(403);
        sendResponse(NOT_LOGGED_IN);
    }
    $token = verifyToken(getToken());

    //鉴权
    $uid = $token->uid;
    $auth = unserialize(DB::get()
        ->prepare("SELECT `jobs` FROM `users` WHERE `uid` = :uid;")
        ->execute(array(
            'uid' => $uid
        ))->fetch()[0]);

    if(array_search('admin', $auth) === false &&
        array_search('su', $auth) === false
    ){
        //仅返回自己的信息
        $info = DB::get()
            ->prepare("SELECT * FROM `users` WHERE `uid` = :uid;")
            ->execute(array(
                'uid' => $uid
            ))->fetch(PDO::FETCH_ASSOC);
    }else{
        //返回其他人信息
        if(!($raw = getRawData())) {
            sendHttpStatus(400);
            sendResponse(EMPTY_BODY);
        }

        if(!($req = parseRawJson($raw))) {
            sendHttpStatus(400);
            sendResponse(WRONG_JSON);
        }

        $queryId = $req['uid'] ?? -1;
        $queryName = $req['UserName'] ?? "";

        $info = DB::get()
            ->prepare("SELECT * FROM `users` WHERE `uid` = :uid OR `user_name` = :user_name;")
            ->execute(array(
                'uid' => $queryId,
                'user_name' => $queryName
            ))->fetch(PDO::FETCH_ASSOC);
    }

    if($info === false) {
        sendHttpStatus(500);
        sendResponse(USER_NOT_FOUND);
    }

    $data = array(
        'UserName' => $info['user_name'],
        'DisplayName' => $info['display_name'],
        'Icon' => $info['avatar'],
        'Point' => $info['point'],
        'Permission' => (unserialize($info['permission']) === false) ? NULL : unserialize($info['permission']),
        'LastLoginAt' => $info['last_time']
    );
    sendResponse(OK, $data);
}

function checkJobs(array $jobs): bool {
    foreach ($jobs as $job) {
        if(array_search($job, JOB_TABLE) === false){
            sendResponse(WRONG_BODY);
        }
    }
    return true;
}

function changeUserInfo() {
    sendResponse(FUNC_DENIED);
}

function changePassword() {
    if(!(isLogin())) {
        sendHttpStatus(403);
        sendResponse(NOT_LOGGED_IN);
    }
    $token = verifyToken(getToken());

    if(!($raw = getRawData())) {
        sendHttpStatus(400);
        sendResponse(EMPTY_BODY);
    }

    if(!($req = parseRawJson($raw))) {
        sendHttpStatus(400);
        sendResponse(WRONG_JSON);
    }

    $uid = $token->uid;
    $password = $req['NewPWD'] ?? getRandPassword();

    $auth = unserialize(DB::get()
        ->prepare("SELECT `jobs` FROM `users` WHERE `uid` = :uid;")
        ->execute(array(
            'uid' => $uid
        ))->fetch()[0]);

    if(array_search('admin', $auth) === false &&
        array_search('su', $auth) === false
    ){
        // 只能修改自己的密码
        $uid = $token['uid'];
    }else {
        // 管理员可以修改其他人的密码
        $uid = (isset($req['uid'])) ? $req['uid'] : $token['uid'];
    }

    $ret = DB::get()
        ->prepare("UPDATE `users` SET `user_password` = :password WHERE `uid` = :uid;")
        ->execute(array(
            'password' => hashPassword($password),
            'uid' => $uid
        ));

    if($ret === false){
        sendHttpStatus(500);
        sendResponse(OPERATION_FAIL);
    }

    $data = array(
        'OriginPWD' => $req['OriginPWD'],
        'NewPWD' => $password
    );
    sendResponse(OK, $data);
}

function changeUserName() {
    if(!(isLogin())) {
        sendHttpStatus(403);
        sendResponse(NOT_LOGGED_IN);
    }
    $token = verifyToken(getToken());

    if(!($raw = getRawData())) {
        sendHttpStatus(400);
        sendResponse(EMPTY_BODY);
    }

    if(!($req = parseRawJson($raw))) {
        sendHttpStatus(400);
        sendResponse(WRONG_JSON);
    }

    $name = $req['DisplayName'] ?? getRandName();
    $uid = $token->uid;

    $auth = unserialize(DB::get()
        ->prepare("SELECT `jobs` FROM `users` WHERE `uid` = :uid;")
        ->execute(array(
            'uid' => $uid
        ))->fetch()[0]);

    if(array_search('admin', $auth) === false &&
        array_search('su', $auth) === false
    ){
        // 只能修改自己的昵称
        $uid = $token['uid'];
    }else {
        // 管理员可以修改其他人的昵称
        $uid = (isset($req['uid'])) ? $req['uid'] : $token['uid'];
    }

    $ret = DB::get()
        ->prepare("UPDATE `users` SET `display_name` = :display_name WHERE `uid` = :uid;")
        ->execute(array(
            'display_name' => $name,
            'uid' => $uid
        ));
    if($ret === false){
        sendHttpStatus(500);
        sendResponse(OPERATION_FAIL);
    }

    $data = ['NewName' => $name];
    sendResponse(OK, $data);
}

function changeUserJobs() {
    if(!(isLogin())) {
        sendHttpStatus(403);
        sendResponse(NOT_LOGGED_IN);
    }
    $token = verifyToken(getToken());

    if(!($raw = getRawData())) {
        sendHttpStatus(400);
        sendResponse(EMPTY_BODY);
    }

    if(!($req = parseRawJson($raw))) {
        sendHttpStatus(400);
        sendResponse(WRONG_JSON);
    }

    if(!isset($req['JobsGroup']) || !isset($req['uid'])) {
        sendHttpStatus(400);
        sendResponse(WRONG_BODY);
    }
    $jobs = $req['JobsGroup'];
    $uid = $token->uid;

    $auth = unserialize(DB::get()
        ->prepare("SELECT `jobs` FROM `users` WHERE `uid` = :uid;")
        ->execute(array(
            'uid' => $uid
        ))->fetch()[0]);

    if(array_search('admin', $auth) === false &&
        array_search('su', $auth) === false
    ){
        sendHttpStatus(403);
        sendResponse(PERMISSION_DENIED);
    }else {
        $uid = $req['uid'];
    }

    checkJobs($jobs);

    $ret = DB::get()
        ->prepare("UPDATE `users` SET `jobs` = :jobs WHERE `uid` = :uid;")
        ->execute(array(
            'jobs' => serialize($jobs),
            'uid' => $uid
        ));
    if($ret === false){
        sendHttpStatus(500);
        sendResponse(OPERATION_FAIL);
    }

    $data = ['NewJobs' => $jobs];
    sendResponse(OK, $data);
}