<?php

require_once 'config.php';

class DB {
    /** @var PDO */
    private $_db;

    /** @var PDOStatement */
    private $_statement;

    private static $_instance = NULL;

    private function __construct() {
        $this->_db = $this->dbConnect();
    }

    public static function get(): DB {
        return self::$_instance ?? (self::$_instance = new DB());
    }

    private function dbConnect():PDO {
        $SERVER = SERVER;
        $DB_NAME = DB_NAME;
        $DB_PASSWD = DB_PASSWD;
        $DB_USER = DB_USER;
        $CHARSET = CHARSET;

        $pdo = new PDO("mysql:dbname={$DB_NAME};host={$SERVER};port=3306", $DB_USER, $DB_PASSWD);
        $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
        $pdo->exec("SET NAMES '{$CHARSET}'");
        return $pdo;
    }

    public function prepare(string $sql): DB {
        $this->_statement = $this->_db->prepare($sql);
        return $this;
    }

    public function execute(array $args = NULL){
        if ($args === NULL) {
            $ret = $this->_statement->execute();
        } else {
            $ret = $this->_statement->execute($args);
        }

        if($ret) {
            return $this;
        }else {
            return false;
        }
    }

    public function fetchAll($mode = PDO::FETCH_BOTH) {
        return $this->_statement->fetchAll($mode);
    }

    public function fetch($mode = PDO::FETCH_BOTH, $cursorOrientation = PDO::FETCH_ORI_NEXT, $cursorOffset = 0) {
        return $this->_statement->fetch($mode, $cursorOrientation, $cursorOffset);
    }

    public function fetchObject($class = "stdClass", array $ctorArgs = array()) {
        return $this->_statement->fetchObject($class, $ctorArgs);
    }

    public function fetchColumn($column = 0) {
        return $this->_statement->fetchColumn($column);
    }
}