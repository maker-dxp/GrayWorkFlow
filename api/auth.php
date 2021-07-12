<?php
// error_reporting (E_ALL || ~E_NOTICE);
ini_set("display_errors", "On");
error_reporting(E_ALL | E_STRICT);

function passCrypt($password){
    $salt = salt($password);
    $password=crypt($password,$salt);
    return $password;
}

//生成密码盐
function salt($password){
    $password=md5($password);
    $password = md5($password."saltsalt");
    $salt=substr($password,0,5);
    return $salt;
}

use Firebase\JWT\JWT;
include_once 'Firebase/JWT/JWT.php';
require_once 'Firebase/JWT/SignatureInvalidException.php';
require_once 'Firebase/JWT/BeforeValidException.php';
require_once 'Firebase/JWT/ExpiredException.php';

function is_get():bool{return $_SERVER['REQUEST_METHOD'] == 'GET' ? true : false;}
function is_post():bool{return $_SERVER['REQUEST_METHOD'] == 'POST' ? true : false;}
function _echo($i){echo $i;return true;}

function createToken()
{
    $key = '344'; //key，唯一标识
    $time = time(); //当前时间
    $token = [
        'iat' => $time, //签发时间
        'nbf' => $time , //(Not Before)：某个时间点后才能访问，比如设置time+30，表示当前时间30秒后才能使用
        'exp' => $time+7200, //过期时间,这里设置2个小时
        'data' => [ //自定义信息，不要定义敏感信息
            'device_id' => 'asdfghj',
        ]
    ];
    $token = JWT::encode($token, $key,'HS256'); //签发token
    $data = ['error'=>0,'mgs'=>'success','data'=>['token'=>$token]];
    return json_encode($data);
}

// $token：签发的token
function verifyToken($token,$ifstander=false){
    $key = '344'; //key要和签发的时候一样，唯一标识
    try {
        JWT::$leeway = 60;//当前时间减去60，把时间留点余地
        $decoded = JWT::decode($token, $key, ['HS256']); //HS256方式，这里要和签发的时候对应
        $arr = (array)$decoded;
        // print_r($arr);
        return true;
    } catch(\Firebase\JWT\SignatureInvalidException $e) {  //签名不正确
        // echo $e->getMessage();
        echo('{"success":false,"message":"'.$e->getMessage().'"}');
        return false;
    }catch(\Firebase\JWT\BeforeValidException $e) {  // 签名在某个时间点之后才能用
        // echo $e->getMessage();
        echo('{"success":false,"message":"'.$e->getMessage().'"}');
        return false;
    }catch(\Firebase\JWT\ExpiredException $e) {  // token过期
        // echo $e->getMessage();
        echo('{"success":false,"message":"'.$e->getMessage().'"}');
        return false;
    }catch(Exception $e) {  //其他错误
        // echo $e->getMessage();
        echo('{"success":false,"message":"'.$e->getMessage().'"}');
        return false;
    }
}

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

function getKeyinArrayNum($key,$array){
    $n = 0;
    foreach(array_keys($array) as $each){
        // echo($each.' '.$key.'|');
        if($key==$each){
            return $n;
        }
        $n++;
    }
    return -1;
}

function analyJson($json_str){
    $out = json_decode($json_str,true);
    if(!json_last_error() == JSON_ERROR_NONE){
        // echo(json_last_error());
        return false;
    } else {
        return $out;
    }
}

function getBearerToken(){
    $headers = getAuthorizationHeader();
    // HEADER: Get the access token from the header
    if (!empty($headers)) {
        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }
    }
    return null;
}




header('Content-type: application/json');
switch ($_GET['action']){
case "getAuthorizationHeader":
    echo(getBearerToken());
    break;
case "verifyToken":
    verifyToken(getBearerToken());
    break;
case "createToken":
    echo(createToken());
    break;
case "login":
    !file_get_contents("php://input")&&_echo('{"success":false,"status_code":"400","message":"Body为空"}')&&exit();
    !analyJson(file_get_contents("php://input"))&&_echo('{"success":false,"status_code":"400","message":"Json格式有误"}')&&exit();
    $in = analyJson(file_get_contents("php://input"));
    $se = ['Test'=>'123456','2333'=>'123123'];
    if(array_key_exists('UserName',$in) && array_key_exists('Password',$in)){
        if($in['Password']==$se[$in['UserName']]){
            $token = json_decode(createToken(),true)['data']['token'];
            $username = $in['UserName'];
            $id = getKeyinArrayNum($in['UserName'],$se);
            // echo('{"success":true,"status_code":"200","UserName": "Test","UserId": 1,"Token": "'.json_decode(createToken(),true)['data']['token'].'","message":"登录成功"}');
            $data = ['message'=>'登录成功','code'=>200,'data'=>['UserName'=>$username,'Token'=>$token,'id'=>$id]];
            echo(json_encode($data));
        } else {
            echo('{"success":false,"status_code":"403","message":"密码不正确"}');
        }
    } else {
        echo('{"success":false,"status_code":"400","message":"用户名或密码缺失"}');
    }
    break;
case "createUser":
    // if(verifyToken(getBearerToken())){
        !file_get_contents("php://input")&&_echo('{"success":false,"status_code":"400","message":"Body为空"}')&&exit();
        !analyJson(file_get_contents("php://input"))&&_echo('{"success":false,"status_code":"400","message":"Json格式有误"}')&&exit();
        $in = json_decode(file_get_contents("php://input"),true);
        if(array_key_exists('UserName',$in) && array_key_exists('Password',$in)){
            $username = $in['UserName'];
            $password = $in['Password'];
            $data = ['message'=>'创建成功','code'=>200,'data'=>['UserName'=>$username,'Password'=>$password,'id'=>1]];
            echo(json_encode($data));    
        }
    // } 
    break;
    default:
    echo('{"success":false,"status_code":"400","message":"无效的请求"}');
}

?>