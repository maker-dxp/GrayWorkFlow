<?php
// error_reporting (E_ALL || ~E_NOTICE);
ini_set("display_errors", "On");
error_reporting(E_ALL | E_STRICT);
define('IN_SYS', TRUE);
require_once 'function.php';

use Firebase\JWT\JWT;

function createToken(string $username,int $userid,int $usergroup=-1){
    include_once 'Firebase/JWT/JWT.php';
    include_once 'Firebase/JWT/SignatureInvalidException.php';
    include_once 'Firebase/JWT/BeforeValidException.php';
    include_once 'Firebase/JWT/ExpiredException.php';
    $key = '344'; //key，唯一标识
    $time = time(); //当前时间
    $token = [
        'iat' => $time, //签发时间
        'nbf' => $time , //(Not Before)：某个时间点后才能访问，比如设置time+30，表示当前时间30秒后才能使用
        'exp' => $time+7200, //过期时间,这里设置2个小时
        'data' => [ //自定义信息，不要定义敏感信息
            'username' => $username,
            'userid' => $userid,
            'usergroup' => $usergroup
        ]
    ];
    $token = JWT::encode($token, $key,'HS256'); //签发token
    return $token;
}

// $token：签发的token
function verifyToken(string $token){
    include_once 'Firebase/JWT/JWT.php';
    include_once 'Firebase/JWT/SignatureInvalidException.php';
    include_once 'Firebase/JWT/BeforeValidException.php';
    include_once 'Firebase/JWT/ExpiredException.php';
    //验证 JWT
    $key = '344';
    try {
        JWT::$leeway = 60;  //允许的时间误差,单位:min
        $decoded = JWT::decode($token, $key, ['HS256']);
        $arr = (array)$decoded;
        // print_r($arr);
        return $arr;
    } catch(Exception $e) {  //捕获所有异常
        //\Firebase\JWT\SignatureInvalidException | \Firebase\JWT\BeforeValidException | \Firebase\JWT\ExpiredException | 
        echo('{"message":"'.$e->getMessage().'","code":4011}');
        sendHttpStatus(401);
        return false;
    }
}

function getAuthorizationHeader(){
    //获取 Header Authorization 中的数据
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

function getBearerToken(){
    //从 Header Authorization 中获取 Token
    $headers = getAuthorizationHeader();
    if (!empty($headers)) {
        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }
    }
    return null;
}

function analyJson(string $json_str){
    //验证传入 JSON 的格式并返回 Array, 若格式有误则返回 False
    $out = json_decode($json_str,true);
    if(!json_last_error() == JSON_ERROR_NONE){
        // echo(json_last_error());
        return false;
    } else {
        return $out;
    }
}

function dologin(){
    !is_post()&&_echo('{"message":"Method Not Allowed","code":405}')&&sendHttpStatus(405)&&exit();
    !file_get_contents("php://input")&&_echo('{"message":"Body为空","code":4002}')&&sendHttpStatus(400)&&exit();
    !analyJson(file_get_contents("php://input"))&&_echo('{"message":"Json格式有误","code":4001}')&&sendHttpStatus(400)&&exit();
    //判断请求合法性
    $in = analyJson(file_get_contents("php://input"));
    if(array_key_exists('UserName',$in) && array_key_exists('Password',$in)){
        include_once 'db.php';
        $username = $in['UserName'];
        $password = $in['Password'];
        $req = verifyPassword($conn,$username,$password);
        !$req&&_echo('{"message":"用户名或密码不正确","code":4010}')&&sendHttpStatus(401)&&exit();
        $id = $req[1];
        $token = createToken($username,$id);
        $data = ['message'=>'登录成功','code'=>200,'data'=>['UserName'=>$username,'Token'=>$token,'id'=>$id]];
        echo(json_encode($data));
    } else {
        echo('{"message":"非法请求","code":4002}');
        sendHttpStatus(400);
    }
}

function doCreateUser(){
    !is_post()&&_echo('{"message":"Method Not Allowed","code":405}')&&sendHttpStatus(405)&&exit();
    !verifyToken(getBearerToken())&&exit();
    !file_get_contents("php://input")&&_echo('{"message":"Body为空","code":4000}')&&sendHttpStatus(400)&&exit();
    !analyJson(file_get_contents("php://input"))&&_echo('{"message":"Json格式有误","code":4001}')&&sendHttpStatus(400)&&exit();
    //判断请求合法性
    $in = json_decode(file_get_contents("php://input"),true);
    if(array_key_exists('UserName',$in) && array_key_exists('Password',$in) && array_key_exists('UserQQ',$in)){
        $username = $in['UserName'];
        $password = $in['Password'];
        $qq = $in['UserQQ'];
        include 'db.php';
        $id = addUser($conn,$username,$password,$qq);
        !$id&&_echo('{"message":"用户名已存在","code":4090}')&&sendHttpStatus(409)&&exit();
        $data = ['message'=>'创建成功','code'=>200,'data'=>['UserName'=>$username,'Password'=>$password,'id'=>$id]];
        echo(json_encode($data));
    } else {
        echo('{"message":"Body字段缺失","code":4002}');
        sendHttpStatus(400);
    }
}

header('Content-type: application/json');
switch ($_GET['action']){
case "login":
    dologin();
    break;
case "createUser":
    doCreateUser();
    break;
    default:
    echo('{"message":"无效的请求","code":400}');
    sendHttpStatus(400);
}

?>