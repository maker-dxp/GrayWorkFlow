<?php

use Firebase\JWT\JWT;

class Widget_User extends Zen_Widget{
    /* 验证方式 */
    private const   S_UID       =       0;
    private const   S_TOKEN     =       1;
    private const   S_PASSWD    =       2;

    /**
     * 可写入字段
     */
    private const WRITEABLE_FIELD    =       array(
        'display_name', 'avatar', 'jobs', 'point', 'last_time'
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
     * @var array
     */
    private $_permission = array();

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
     * Widget_User constructor.
     *
     * @param mixed $data   任意类型的用于用户验证的数据
     * @param int $switch
     * @throws Widget_User_Exception|Zen_DB_Exception
     */
    private function __construct($data, int $switch) {
        $this->_db = Zen_DB::get();
        $sql = $this->_db
            ->select('display_name', 'avatar', 'jobs', 'point', 'create_time', 'last_time')
            ->from('users');
        
        switch($switch) {
            case self::S_UID:       //uid方式创建
                $this->_uid = $data;
                $sql->where('uid = ?', $this->_uid)
                    ->limit(1);
                break;
            case self::S_TOKEN:     //token方式创建
                $this->verifyToken($data);
                $this->_uid = $this->_token_data['uid'];
                $sql->where('uid = ?', $this->_uid)
                    ->limit(1);
                break;
            case self::S_PASSWD:
                $user_name = $data['user_name'];
                $user_passwd = $data['user_passwd'];
                $sql->where('user_name = ?', $user_name)
                    ->where('user_password = ?', $this->hashPassword($user_passwd))
                    ->limit(1);
                break;
            default:
                throw new Widget_User_Exception('无法构建用户', HTTP_SERVER_ERROR);
        }

        $this->_data = $this->_db->fetchRow($sql);
        if(empty($this->_data)) {
            throw new Widget_User_Exception('用户不存在', 4013);
        }

        $this->_permission = unserialize($this->_data['jobs']);
    }

    /**
     * 验证一个用户是否合法同时返回一个用户对象
     *
     * @param int|string 传入一个uid或token
     * @throws Widget_User_Exception|Zen_DB_Exception
     */
    public static function verify(): Widget_User {
        $identity = func_get_arg(0);

        switch (true) {
            case is_int($identity):
                return new Widget_User($identity,self::S_UID);
            case is_string($identity):
                return new Widget_User($identity, self::S_TOKEN);
            case is_array($identity):
                return new Widget_User($identity, self::S_PASSWD);
            default:
                throw new Widget_User_Exception('无法构建用户', HTTP_SERVER_ERROR);
        }
    }

    /**
     * 创建一个新用户
     *
     * @param array $data
     * @throws Widget_User_Exception
     * @throws Zen_DB_Exception
     */
    public function create(array $data) {
        if(!$this->isAdmin()) {
            throw new Widget_User_Exception('只有管理员可以创建用户', PERMISSION_DENIED);
        }

        if(!isset($data['user_name'])) {
            throw new Widget_User_Exception('缺少用户名', EMPTY_BODY_FIELD);
        }

        $sql = $this->_db
            ->insert('users')
            ->rows(array(
               'user_name' => $data['user_name'],
               'user_password' => isset($data['user_password']) ?
                   $this->hashPassword($data['user_password']) :
                   $this->hashPassword($this->getRandPassword()),
                'display_name' => $data['display_name'] ?? $this->getRandName(),
                'avatar' => $data['avatar'] ?? DEFAULT_AVATAR,
                'jobs' => serialize(array()),
                'point' => 0,
                'create_time' => date('Y-m-d'),
                'last_time' => '1970-1-1'
            ));
        return $this->_db->query($sql);
    }

    /**
     * 发送token到响应头
     */
    public function sendToken() {
        if(empty($this->_token)) {
            throw new Widget_User_Exception('没有有效token可以设置', HTTP_SERVER_ERROR);
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
        return $this->_data[$name] ?? '';
    }

    /**
     * @param $key
     * @param $value
     * @throws Widget_User_Exception
     */
    public function __set($key, $value) {
        if(!in_array($key, self::WRITEABLE_FIELD)) {
            throw new Widget_User_Exception('尝试在不可写入的字段进行写入操作', PERMISSION_DENIED);
        }

        $this->_data[$key] = $value;
        if($key == 'jobs') {
            //刷新对象数据
            $this->_permission = $value;
            $value = serialize($value);
        }

        $this->_cache[$key] = $value;
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
     */
    public function save() {
        $sql = $this->_db
            ->update('users')
            ->rows($this->_cache)
            ->where('uid = ?', $this->_uid);
        $ret = $this->_db->query($sql);

        if($ret != 1) {
            throw new Widget_User_Exception('保存数据时出错', HTTP_SERVER_ERROR);
        }

        // 清空缓存
        $this->_cache = array();
    }

    /**
     * 创建一个token
     *
     * @param $display_name
     * @param $uid
     * @return string
     */
    private function createToken($display_name, $uid): string {
        $time = time(); //当前时间
        $payload = [
            'iat' => $time, //签发时间
            'nbf' => $time , //(Not Before)：某个时间点后才能访问，比如设置time+30，表示当前时间30秒后才能使用
            'exp' => $time+7200, //过期时间,这里设置2个小时
            'data' => [ //自定义信息，不要定义敏感信息
                'DisplayName' => $display_name,
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
     * @return false
     */
    private function verifyToken(string $token) {
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
    private function isAdmin() {
        foreach ($this->_permission as $item) {
            if($item == SU || $item == ADMIN) {
                return true;
            }
        }
        return false;
    }
}