<?php

/**
 * 该文件用于完成视频字幕工作区模块
 */

include_once 'db.php';

const FIELD_DELIMITER       =       ', ';

/* 权限 */
const ADMIN                 =       '';
const TRANS                 =       '';
const TRANS_PROOFREADER     =       '';
const AXIS                  =       '';
const AXIS_PROOFREADER      =       '';

/* 表名 */
const TABLE                 =       '';

/* 字段名 */
const VIDEO_ID              =       '';     // 视频ID
const VIDEO_NAME            =       '';     // 视频名称
const TRANS_DRAFT           =       '';     // 翻译初稿
const TRANS_PROOF           =       '';     // 翻译校对稿
const TRANS_FINAL           =       '';     // 翻译终稿
const AXIS_DRAFT            =       '';     // 时轴初稿
const AXIS_FINAL            =       '';     // 时轴终稿
const COVER_UNTRANSLATED    =       '';     // 原始封面
const COVER_TRANSLATED      =       '';     // 翻译完成的封面
const SCHEDULE              =       '';     // 进度

/* 函数 */
/**
 * 获取正确的字段名
 *
 * @return string
 */
function getField(): string {
    $args = func_get_args();
    $ret = '';

    foreach ($args as $arg) {
        $ret .= '`' . $arg . '`' . FIELD_DELIMITER;
    }
    return rtrim($ret, FIELD_DELIMITER);
}

/**
 * 转换每一行数据
 *
 * @param array $row
 * @return string
 */
function setRows(array $row): string {
    $set = 'SET ';
    foreach($row as $key => $value) {
        switch(true){
            case is_int($value):
            case is_float($value):
            case is_bool($value):
                $value = (string)$value;
                break;
            case is_string($value):
                $value = str_replace('"', '\"', $value);
                $value = '"' . $value . '"';
                break;
            case is_null($value):
                $value = '"null"';
                break;
            case is_array($value):
            case is_object($value):
                $value = serialize($value);
                $value = str_replace('"', '\"', $value);
                $value = '"' . $value . '"';
                break;
            default:
                return false;  // 抛异常
        }
        $key = '`' . $key . '`';
        $set .= "{$key} = {$value}" . FIELD_DELIMITER;
    }
    return rtrim($set, FIELD_DELIMITER);
}

/**
 * 查看视频是否存在
 *
 * @param mysqli $conn
 * @param int $video_id
 * @return bool
 */
function videoIsExist(mysqli $conn, int $video_id): bool {
    // 合成语句
    $id_field = getField(VIDEO_ID);
    $table_field = getField(TABLE);
    $sql = "SELECT {$id_field} FROM {$table_field} WHERE {$id_field} = ? ;";

    //获取
    if(!($stmt = $conn->prepare($sql))) { return false; } // 这里要抛异常
    $stmt->bind_param('i', $video_id);
    $stmt->execute();
    $stmt->store_result();

    if($stmt->num_rows === 0) {
        return false;
    }

    return true;

}
/**
 * 获取工作信息
 *
 * @param mysqli $conn
 * @param int $video_id
 * @param string $authority
 * @return false|string
 */
function getWorkInfo(mysqli $conn, int $video_id, string $authority): ?array {
    /** @var string $field 查询的字段 */
    $field = getField(VIDEO_NAME, COVER_UNTRANSLATED, COVER_TRANSLATED, SCHEDULE) . FIELD_DELIMITER;

    switch ($authority) {
        case TRANS_PROOFREADER:
            $field .= getField(TRANS_FINAL, TRANS_PROOF) . FIELD_DELIMITER;         // 校对可以获取翻译终稿、校对稿以及初稿
        case TRANS:
            $field .= getField(TRANS_DRAFT);                                        // 翻译仅可获得初稿
            break;
        case AXIS_PROOFREADER:
            $field .= getField(AXIS_FINAL) . FIELD_DELIMITER;                       // 时轴校对可以获取时轴终稿和初稿
        case AXIS:
            $field .= getField(AXIS_DRAFT);                                         // 时轴仅可获取初稿
            break;
        case ADMIN:
            $field = '*';                                                           // 管理可以获取所有信息
    }

    $sql = "SELECT {$field} FROM " . getField(TABLE) . "WHERE ( " . getField(VIDEO_ID) . " = ? );";

    if(!($stmt = $conn->prepare($sql))) { return false; }   // 这里要抛异常
    $stmt->bind_param('i', $video_id);
    $stmt->execute();

    $result = $stmt->get_result();

    if($result === false || $result->num_rows === 0) { return null; }  // 没拿到数据

    if(($data = $result->fetch_assoc()) === NULL) { return null; }

    return $data;
}

/**
 * 设置当前工作状态
 *
 * @param mysqli $conn
 * @param array $infos
 * @param string $authority
 * @return bool
 */
function setWorkInfo(mysqli $conn, array $infos, string $authority) {
    /* 优先查看有没有传入id */
    if(!isset($infos[VIDEO_ID])) { return false; } // 抛异常
    $id = $infos[VIDEO_ID];

    /* 查看视频是否存在 */
    if(videoIsExist($conn, $id)) {
        return updateInfo($conn, $infos, $authority);
    }else {
        return insertInfo($conn, $infos, $authority);
    }


}

/**
 * 更新数据
 * 
 * @param mysqli $conn
 * @param array $infos
 * @param string $authority
 * @return bool
 */
function updateInfo(mysqli $conn, array $infos, string $authority): bool {
    /* 优先查看有没有传入id */
    if(!isset($infos[VIDEO_ID])) { return false; } // 抛异常
    if(!is_int($id = $infos[VIDEO_ID])) { return false; } //抛异常

    // 初始化一些变量
    $field_table = getField(TABLE);
    $field_id = getField(VIDEO_ID);
    $table_data = getWorkInfo($conn, $id, $authority);

    // 更新数据
    foreach ($infos as $key => $value) {
        if(isset($table_data[$key])) {
            $table_data[$key] = $value;
        }
    }

    // 合成查询语句
    $field_set = setRows($table_data);
    $sql = "UPDATE {$field_table} {$field_set} WHERE {$field_id} = ? ;";

    if(!($stmt = $conn->prepare($sql))) { return false; }   // 这里要抛异常
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->store_result();

    if($stmt->affected_rows === 0) { return false; }

    return true;
}

/**
 * 插入新数据
 *
 * @param mysqli $conn
 * @param array $infos
 * @param string $authority
 * @return bool
 */
function insertInfo(mysqli $conn, array $infos, string $authority): bool {
    /* 优先查看有没有传入id */
    if(!isset($infos[VIDEO_ID])) { return false; } // 抛异常

    // 构建field
    $field_name = getField(VIDEO_ID, VIDEO_NAME, COVER_UNTRANSLATED, COVER_TRANSLATED, SCHEDULE) . FIELD_DELIMITER;
    $field_table = getField(TABLE);
    $raw_args = array(
        1 => $infos[VIDEO_ID],
        2 => $infos[VIDEO_NAME],
        3 => $infos[COVER_UNTRANSLATED],
        4 => $infos[COVER_TRANSLATED],
        5 => $infos[SCHEDULE]
    );

    /*
     * 后面要把这个改成函数获取，所以写成这个样子
     *
     * 函数 getMark()
     */
    $field_values = '?, ?, ?, ?, ';

    switch ($authority) {
        case TRANS_PROOFREADER:
            $field_name .= getField(TRANS_FINAL, TRANS_PROOF, TRANS_DRAFT) . FIELD_DELIMITER;         // 校对可以设置翻译终稿、校对稿以及初稿
            $raw_args[0] = 'isssssss';      // 这里之后也要改
            $field_values .= '?, ?, ?, ';
            $raw_args += array(
                6 => $infos[TRANS_FINAL],
                7 => $infos[TRANS_PROOF],
                8 => $infos[TRANS_DRAFT]
            );
            break;
        case TRANS:
            $field_name .= getField(TRANS_DRAFT);                                        // 翻译仅可设置初稿
            $field_values .= '?, ';
            $raw_args[0] = 'isssss';
            $raw_args += array(
                6 => $infos[TRANS_DRAFT]
            );
            break;
        case AXIS_PROOFREADER:
            $field_name .= getField(AXIS_FINAL, AXIS_DRAFT) . FIELD_DELIMITER;                       // 时轴校对可以设置时轴终稿和初稿
            $field_values .= '?, ? ';
            $raw_args[0] = 'issssss';
            $raw_args += array(
                6 => $infos[AXIS_FINAL],
                7 => $infos[AXIS_DRAFT]
            );
            break;
        case AXIS:
            $field_name .= getField(AXIS_DRAFT);                                         // 时轴仅可设置初稿
            $field_values .= '?, ';
            $raw_args[0] = 'isssss';
            $raw_args += array(
                6 => $infos[AXIS_DRAFT]
            );
            break;
        case ADMIN:
            $field_name .= getField(TRANS_FINAL, TRANS_PROOF, TRANS_DRAFT, AXIS_FINAL, AXIS_DRAFT);                                                            // 管理可以设置所有信息
            $field_values = '?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ';
            $raw_args[0] = 'isssssssss';
            $raw_args += array(
                6 => $infos[TRANS_FINAL],
                7 => $infos[TRANS_PROOF],
                8 => $infos[TRANS_DRAFT],
                9 => $infos[AXIS_FINAL],
                10 => $infos[AXIS_DRAFT]
            );
    }

    // 数据重排并检查空值
    $args = array();
    for($i = 0; $i < count($raw_args); $i++) {
        $args[] = ($raw_args[$i] === NULL) ? "" : $raw_args[$i];
    }

    $field_values = rtrim($field_values, ", ");
    $sql = "INSERT INTO {$field_table}({$field_name}) VALUES({$field_values});";
    $stmt = $conn->prepare($sql);
    call_user_func_array(array($stmt, 'bind_param'), $args);
    $stmt->execute();
    $stmt->store_result();

    if($stmt->affected_rows === 0) { return false; }

    return true;
}

//const CODE = 200;
//const MESSAGE = 'ok';
//
//$info =  getWorkInfo($conn, 0, TRANS);
//$ret = array(
//    'code' => CODE,
//    'message' => MESSAGE,
//    'data' => $info
//);
//echo json_encode($ret);

//echo setRows(array(
//    VIDEO_ID => 100,
//    VIDEO_NAME => 'Video',
//    SCHEDULE => 'create',
//    'array' => array(
//        'test',
//        'test',
//        'test'
//    ),
//    'null' => null,
//    'object' => $conn
//));

//echo updateInfo($conn, array(
//    VIDEO_ID => 0,
//    VIDEO_NAME => 'Video 3',
//    SCHEDULE => 'trans'
//), ADMIN);

//echo setWorkInfo($conn, array(
//    VIDEO_ID => 4,
//    VIDEO_NAME => 'Video xx',
//    SCHEDULE => 'final',
//    TRANS_PROOF => 'abc'
//), TRANS);