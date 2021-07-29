<?php

class Widget_Options extends Zen_Widget {
    /**
     * 键缓存
     *
     * @var array
     */
    private $_key_cache = array();

    private $_exist_cache = array();

    private $_empty_cache = array();

    /**
     * 获取保存的设置
     *
     * @param string $key
     * @param bool $use_cache
     * @return array|mixed
     */
    public function getOption(string $key, bool $use_cache = true) {
        if($use_cache && isset($this->_key_cache[$key])) {
            return $this->_key_cache[$key];
        }else {
            $res = $this->readValue($key);
        }

        $symbol = substr($res, 0, 2);

        switch($symbol) {
            case 'a.':
            case 'o.':
                $data = unserialize(substr($res, 2));
                break;
            case 'n.':
            case 'r.':
                $data = null;
                break;
            case 's.':
                $data = substr($res, 2);
                break;
            default:
                $data = null;
        }

        $this->_key_cache[$key] = $data; // 存入原始数据
        return $data;
    }

    /**
     * 写入设置
     *
     * @param string $key
     * @param $value
     * @throws Zen_DB_Exception
     * @throws Widget_Exception
     */
    public function setOption(string $key, $value) {
        /*
         * $real_value是写入数据库时的数据
         * $value是原始数据
         * $symbol是数据类型用于转换回原数据
         */
        switch(true){
            case is_array($value):
                $symbol = 'a.';
                $real_value = serialize($value);
                break;
            case is_object($value):
                $symbol = 'o.';
                $real_value = serialize($value);
                break;
            case is_null($value):
                $symbol = 'n.';
                $real_value = 'null';
                break;
            case is_resource($value):
                $symbol = 'r.';
                $real_value = 'resource';       //资源类型不应该被保存
                break;
            default:
                $symbol = 's.';
                $real_value = $value;
        }

        $this->writeValue($key, $symbol . $real_value);

        //存入原始数据
        $this->_key_cache[$key] = $value;
        $this->_exist_cache[$key] = true;
    }

    /**
     * 检查键是否存在
     *
     * @param string $key
     * @param bool $use_cache
     * @return bool
     * @throws Zen_DB_Exception
     */
    public function keyExist(string $key, bool $use_cache = true): bool {
        if($use_cache && isset($this->_exist_cache[$key])) {
            return $this->_exist_cache[$key];
        }

        //缓存未命中或不使用
        $ret = $this->getOption($key);

        $isEmpty = ($ret === null);

        $this->_exist_cache[$key] = !$isEmpty;

        return !$isEmpty;
    }

    /**
     * 检查键是否为空
     *
     * @param string $key
     * @return bool
     * @throws Zen_DB_Exception
     */
    public function keyEmpty(string $key, bool $use_cache = true): bool {
        if($use_cache && $this->keyExist($key) && isset($this->_empty_cache[$key])) {
            return $this->_empty_cache[$key];
        }

        //缓存未命中或不使用
        $res = $this->getOption($key);
        if(empty($res)) {
            $this->_empty_cache[$key] = true;
        }else {
            $this->_empty_cache[$key] = false;
        }

        return $this->_empty_cache[$key];
    }

    private function readValue(string $key) {
        $db = Zen_DB::get();

        $sql = $db->select("value")
            ->from("options")
            ->where("key = ?", $key);

        return $db->fetchRow($sql)['value'] ?? '';
    }

    private function writeValue(string $key, string $value) {
        $db = Zen_DB::get(AUTH_WRITE);

        if($this->keyExist($key, false)){
            // 存在则更新
            $sql = $db->update('options')
                ->rows(array(
                    'value' => $value
                ))->where('key = ?', $key);
        }else {
            // 不存在就插入
            $sql = $db->insert('options')
                ->rows(array(
                    'key' => $key,
                    'value' => $value
                ));
        }
        $db->query($sql);
    }
}