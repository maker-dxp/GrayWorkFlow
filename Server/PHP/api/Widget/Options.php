<?php

class Widget_Options extends Zen_Widget {
    /**
     * 键缓存
     *
     * @var array
     */
    private $_key_cache = array();

    /**
     * 获取保存的设置
     *
     * @param string $key
     * @param bool $refresh
     * @return array|mixed
     * @throws Zen_DB_Exception
     */
    public function getOption(string $key, bool $refresh = false) {
        if(isset($this->_key_cache[$key]) && !$refresh) {
            return $this->_key_cache[$key];
        }

        $db = Zen_DB::get();

        $sql = $db->select("value")
        ->from("options")
        ->where("key = ?", $key);

        $res = $db->fetchRow($sql);
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

        $this->_key_cache[$key] = $data;
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

        $db = Zen_DB::get(AUTH_WRITE);

        if($this->keyExist($key)){
            // 存在则更新
            $sql = $db->update('options')
                ->rows(array(
                    'value' => $symbol . $real_value
                ))->where('key = ?', $key);
            $ret = $db->query($sql);
            if($ret == 0){
                throw new Widget_Exception('Option: set option failed.', HTTP_SERVER_ERROR);
            }
        }else {
            // 不存在就插入
            $sql = $db->insert('options')
                ->rows(array(
                    'key' => $key,
                    'value' => $real_value
                ));
            $db->query($sql);
        }

        $this->_key_cache[$key] = $value;  //写入cache
    }

    /**
     * 检查键是否存在
     *
     * @param string $key
     * @return bool
     * @throws Zen_DB_Exception
     */
    public function keyExist(string $key): bool {
        if($this->_key_cache[$key] === null) {
            return false;
        }

        $db = Zen_DB::get();
        $sql = $db->select('key')
            ->from('options')
            ->where('key = ?', $key);
        $ret = $db->fetchRow($sql);

        $isEmpty = empty($ret);

        $this->_key_cache[$key] = $isEmpty ? null : $ret;

        return !$isEmpty;
    }

    /**
     * 检查键是否为空
     *
     * @param string $key
     * @return bool
     * @throws Zen_DB_Exception
     */
    public function keyEmpty(string $key): bool {
        if(!isset($this->_key_cache[$key])) {
            return false;
        }elseif (empty($this->_key_cache[$key])){
            return false;
        }

        $db = Zen_DB::get();
        $sql = $db->select('key')
            ->from('options')
            ->where('key = ?', $key);
        $ret = $db->fetchRow($sql);

        if(empty($ret)) {
            $this->_key_cache[$key] = null;
            return false;
        }

        if(empty($ret[$key])) {
            $this->_key_cache[$key] = 0;
            return false;
        }

        $this->_key_cache[$key] = $ret[$key];
        return true;
    }
}