<?php

use Firebase\JWT\JWT;

/* 赋权掩码 */
const P_BAN             =           0;
const P_TRANS           =           1;
const P_TRANSPROOF      =           3;
const P_AXIS            =           4;
const P_AXISPROOF       =           12;
const P_COMPRESSION     =           16;
const P_AFTEREFFECT     =           32;
const P_OPERATION       =           64;
const P_ADMIN           =           128;
const P_ALL             =           255;

/* 鉴权掩码 */
const C_NULL            =           0;
const C_TRANS           =           1;
const C_TRANSPROOF      =           2;
const C_AXIS            =           4;
const C_AXISPROOF       =           8;
const C_COMPRESSION     =           16;
const C_AFTEREFFECT     =           32;
const C_OPERATION       =           64;
const C_ADMIN           =           128;
const C_ALL             =           255;

/* 默认头像位置 */
const DEFAULT_AVATAR    =           '';

class Widget_Users extends Widget_Api {
    /* 验证方式 */
    private const   S_UID       =       0;
    private const   S_TOKEN     =       1;
    private const   S_PASSWD    =       2;

    /**
     * 权限转换表
     */
    private const AUTH_TABLE    = array(
        C_NULL          =>      'ban',
        C_TRANS         =>      'translation',
        C_TRANSPROOF    =>      'translation_proofreading',
        C_AXIS          =>      'time_axis',
        C_AXISPROOF     =>      'time_axis_proofreading',
        C_COMPRESSION   =>      'compression',
        C_AFTEREFFECT   =>      'back_support',
        C_ADMIN         =>      'admin',
        C_ALL           =>      'super_admin'
    );

    /**
     * 可写入字段
     */
    private const WRITEABLE_FIELD    =       array(
        'user_password', 'nick_name', 'avatar', 'authority', 'point', 'last_time', 'qq'
    );

    /**
     * JWT加密密钥
     */
    private const JWT_KEY = 'EgokP7Kkard4byFvoyvveE4Q';

    /**
     * Hash加密密钥
     */
    private const PWD_KEY = 'M8sjfLyfVmUDUPmq';

    /**
     * 数据库对象
     *
     * @var Zen_DB
     */
    private $_db = null;

    /**
     * 解析出的token数据
     *
     * @var array
     */
    private $_token_data = array();

    /**
     * 当前用户token
     *
     * @var string
     */
    private $_token = '';

    /**
     * 当前用户uid
     *
     * @var int
     */
    private $_uid = 0;

    /**
     * 当前用户权限组
     *
     * @var int
     */
    private $_permission = 0;

    /**
     * 从数据库中获取的其他数据
     *
     * @var array
     */
    private $_data = array();

    /**
     * 写入缓存
     *
     * @var array
     */
    private $_cache = array();

    /**
     * Widget_Users constructor.
     *
     * @param mixed $data   任意类型的用于用户验证的数据
     * @param int $switch
     * @throws Widget_Users_Exception|Zen_DB_Exception
     */
    private function __construct($data, int $switch) {
        $this->_db = Zen_DB::get();
        $sql = $this->_db
            ->select('nick_name', 'avatar', 'authority', 'point', 'qq', 'create_time', 'last_time')
            ->from('users');
        
        switch($switch) {
            case self::S_UID:       //uid方式创建
                $this->_uid = $data;
                $sql->where('uid = ?', $this->_uid)
                    ->limit(1);
                $isLogin = false;
                break;
            case self::S_TOKEN:     //token方式创建
                if(!$this->verifyToken($data)) {
                    throw new Widget_Users_Exception('无法构建用户', TOKEN_IS_INCORRECT);
                }
                $this->_uid = $this->_token_data['uid'];
                $sql->where('uid = ?', $this->_uid)
                    ->limit(1);
                $isLogin = true;
                break;
            case self::S_PASSWD:    //密码方式验证
                if(!isset($data['user_name']) || !isset($data['user_passwd'])){
                    throw new Widget_Users_Exception('无法构建用户', HTTP_SERVER_ERROR);
                }
                $user_name = $data['user_name'];
                $user_passwd = $data['user_passwd'];
                $sql->where('user_name = ?', $user_name)
                    ->where('user_password = ?', $this->hashPassword($user_passwd))
                    ->limit(1);
                $isLogin = true;
                break;
            default:
                throw new Widget_Users_Exception('无法构建用户', HTTP_SERVER_ERROR);
        }

        /* 获取用户信息 */
        $this->_data = $this->_db->fetchRow($sql);
        if(empty($this->_data)) {
            $this->error("Login", "User not found");
            throw new Widget_Users_Exception('用户不存在', USER_NOT_FOUND);
        }

        /* 登录后更改登录时间 */
        if($isLogin) {
            $sql = $this->_db
                ->update('users')
                ->rows(array(
                    'last_time' => date('Y-m-s H:i:s')
                ));
            $this->_db->query($sql);
            $this->log("Login");
        }

        $this->_permission = $this->_data['authority'];
        unset($this->_data['authority']);
    }

    /**
     * 最后进行一次保存
     *
     * @throws Widget_Users_Exception
     */
    public function __destruct() {
        if(!empty($this->_cache)) {
            $this->save();
        }
    }

    /**
     * 获取权限字符串数组
     *
     * @param int $auth
     * @return array|string[]
     */
    public function authTransToStringArray(int $auth): array {
        if($auth & C_NULL) {
            return array(self::AUTH_TABLE[C_NULL]);
        }

        if($auth & C_ALL) {
            return array(self::AUTH_TABLE[C_ALL]);
        }

        $auth_arr = array();
        foreach (self::AUTH_TABLE as $key => $value) {
            if($auth & $key) {
                $auth_arr[] = $value;
            }
        }
        return $auth_arr;
    }

    /**
     * 转换权限字符串数组为整数
     *
     * @param array $authorities
     * @return false|int|string
     */
    public function authTransToInt(array $authorities) {
        if(in_array('super_admin', $authorities)) {
            return P_ALL;
        }

        if(in_array('ban', $authorities)) {
            return P_BAN;
        }

        $auth = P_BAN;
        foreach ($authorities as $authority) {
            $auth |= array_search($authority, self::AUTH_TABLE) ?? 0;
        }
        return $auth;
    }

    /**
     * 验证一个用户是否合法同时返回一个用户对象
     *
     * @param int|string|array 传入一个uid/token或用户名密码数组
     * @throws Widget_Users_Exception|Zen_DB_Exception
     */
    public static function verify($identity): Widget_Users {
        switch (true) {
            case is_string($identity):
                return new Widget_Users($identity, self::S_TOKEN);
            case is_array($identity):
                return new Widget_Users($identity, self::S_PASSWD);
            default:
                throw new Widget_Users_Exception('无法构建用户', OPERATION_FAIL);
        }
    }

    /**
     * 创建一个用户对象
     *
     * @param int $uid
     * @return Widget_Users
     * @throws Widget_Users_Exception
     * @throws Zen_DB_Exception
     */
    public static function factory(int $uid): Widget_Users {
        return new Widget_Users($uid, self::S_UID);
    }

    /**
     * 创建一个新用户
     *
     * @param array $data
     * @return int
     * @throws Widget_Users_Exception
     */
    public function create(array $data): int {
        if(!$this->isAdmin()) {
            $this->error("Create User", "Permission denied.");
            throw new Widget_Users_Exception('只有管理员可以创建用户', PERMISSION_DENIED);
        }

        if(!isset($data['UserName'])) {
            throw new Widget_Users_Exception('缺少用户名', EMPTY_BODY_FIELD);
        }

        try{
            $sql = $this->_db
                ->insert('users')
                ->rows(array(
                    'user_name'              => $data['UserName'],
                    'user_password'          => isset($data['Password']) ?
                        $this->hashPassword($data['Password']) :
                        $this->hashPassword($this->getRandPassword()),
                    'nick_name'              => $data['NickName'] ?? $this->getRandName(),
                    'avatar'                 => $data['Icon'] ?? DEFAULT_AVATAR,
                    'authority'              => P_BAN,
                    'point'                  => 0,
                    'qq'                     => $data['QQ'],
                    'create_time'            => date('Y-m-d'),
                    'last_time'              => '1970-1-1'
                ));
            return $this->_db->query($sql);
        }catch(Zen_DB_Exception $e) {
            throw new Widget_Users_Exception('服务器错误', HTTP_SERVER_ERROR);
        }

    }

    /**
     * 发送token到响应头
     */
    public function sendToken() {
        if(empty($this->_token)) {
            $this->_token = $this->createToken($this->_data['nick_name'], $this->_uid);
        }

        Zen_Response::getInstance()->setHeader('Access-Token', $this->_token);
    }

    /**
     * 获取用户数据
     *
     * @param $name
     * @return mixed|string
     */
    public function __get($name) {
        if($name == 'authority') {
            return $this->authTransToStringArray($this->_permission);
        }

        return $this->_data[$name] ?? '';
    }

    /**
     * 设置用户数据
     *
     * @param $key
     * @param $value
     * @throws Widget_Users_Exception
     */
    public function __set($key, $value) {
        if(!in_array($key, self::WRITEABLE_FIELD)) {
            $this->error("Modify User Data", "Permission denied.");
            throw new Widget_Users_Exception('写入失败', WRONG_BODY);
        }

        switch ($key) {
            case 'authority':
                $value = $this->grantAuth($value);
                break;
            case 'user_password':
                $value = $this->hashPassword($value);
                break;
            default:
        }

        $this->_cache[$key] = $value;
    }

    /**
     * 赋予权限
     *
     * @param mixed $auth
     * @throws Widget_Users_Exception
     */
    public function grantAuth($auth) {
        if(!$this->isAdmin()) {
            $this->error("Grant User Authority", "Permission denied.");
            throw new Widget_Users_Exception('只有管理员可以修改用户权限', PERMISSION_DENIED);
        }

        switch (true) {
            case is_string($auth):
                $auth = array_search($auth, self::AUTH_TABLE);
                break;
            case is_array($auth):
                $auth = $this->authTransToInt($auth);
                break;
            default:
        }
        $this->_permission ^= $auth;
        return $this->_permission;
    }

    /**
     * 增加积分
     *
     * @param int $point
     */
    public function addPoint(int $point) {
        if(!isset($this->_cache['point'])) {
            $this->_cache['point'] = $this->_data['point'] ?? 0;
        }

        $this->_cache['point'] += $point;
    }

    /**
     * 消费积分
     *
     * @param int $point
     */
    public function rewardPoint(int $point) {
        if(!isset($this->_cache['point'])) {
            $this->_cache['point'] = $this->_data['point'] ?? 0;
        }else {
            $this->_cache['point'] -= $point;
        }
    }

    /**
     * 保存缓存中的数据
     *
     * @throws Widget_Users_Exception
     */
    public function save() {
        try{
            $sql = $this->_db
                ->update('users')
                ->rows($this->_cache)
                ->where('user_id = ?', $this->_uid);
            $this->_db->query($sql);
        }catch (Zen_DB_Exception $e) {
            $this->error("Save User Data", "Database Error");
            throw new Widget_Users_Exception('保存数据时出错', HTTP_SERVER_ERROR);
        }

        // 清空缓存
        $this->_cache = array();
    }

    /**
     * 创建一个token
     *
     * @param $nick_name
     * @param $uid
     * @return string
     */
    private function createToken($nick_name, $uid): string {
        $time = time(); //当前时间
        $payload = [
            'iat' => $time, //签发时间
            'nbf' => $time , //(Not Before)：某个时间点后才能访问，比如设置time+30，表示当前时间30秒后才能使用
            'exp' => $time+7200, //过期时间,这里设置2个小时
            'data' => [ //自定义信息，不要定义敏感信息
                'NickName' => $nick_name,
                'uid' => $uid
            ]
        ];

        //签发token
        $this->_token = JWT::encode($payload, self::JWT_KEY,'HS256');
        return $this->_token;
    }

    /**
     * 验证一个token
     *
     * @param string $token
     * @return bool
     */
    private function verifyToken(string $token): bool {
        try {
            JWT::$leeway = 60;  //允许的时间误差,单位:min
            $this->_token_data = JWT::decode($token, self::JWT_KEY, ['HS256'])['data'];
            return true;
        } catch(UnexpectedValueException $e) {  //捕获所有异常
            return false;
        }
    }

    /**
     * 获取随机密码
     *
     * @return string
     */
    private function getRandPassword(): string {
        return substr(md5(time()), 0, 16);
    }

    /**
     * 获取随机名称
     *
     * @return string
     */
    private function getRandName(): string {
        return 'user_' . substr(md5(time()), 0, 8);
    }

    /**
     * 对密码进行加密
     *
     * @param string $password
     * @return bool
     */
    private function hashPassword(string $password): bool {
        return hash_hmac('sha256', "Gray" . $password . "Wind", self::PWD_KEY);
    }

    /**
     * 检查当前用户是否为管理员
     *
     * @return bool
     */
    public function isAdmin(): bool {
        return (bool)($this->_permission & C_ADMIN);
    }

    /**
     * 输出日志记录
     *
     * @param string $type
     * <p>
     *  传入的字符串为需要在日志中显示的操作。
     *  如： 登录操作传入"Login"
     * </p>
     */
    public function log(string $type) {
        $messages  = 'Users'                                                         . PHP_EOL;
        $messages .= "[Date]\t\t"        . date('Y-m-d H:i:s')                   . PHP_EOL;
        $messages .= "[Type]\t\t"        . $type                                     . PHP_EOL;
        $messages .= "[UID]\t\t"         . $this->_uid                               . PHP_EOL;
        $messages .= "[NickName]\t"      . $this->_data['nick_name']?? 'unknown'     . PHP_EOL;
        self::iLog($messages);
    }

    /**
     * 输出错误记录
     *
     * @param string $type
     * @param string $message
     */
    public function error(string $type, string $message) {
        $messages  = 'Users'                                                         . PHP_EOL;
        $messages .= "[Date]\t\t"        . date('Y-m-d H:i:s')                   . PHP_EOL;
        $messages .= "[Type]\t\t"        . $type                                     . PHP_EOL;
        $messages .= "[UID]\t\t"         . $this->_uid                               . PHP_EOL;
        $messages .= "[NickName]\t"      . $this->_data['nick_name']?? 'unknown'     . PHP_EOL;
        $messages .= "[Message]\n"       . $message                                  . PHP_EOL;
        self::wLog($messages);
    }
}