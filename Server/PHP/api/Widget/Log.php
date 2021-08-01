<?php

class Widget_Log {
    const L_ERROR           =       0;
    const L_WARNING         =       1;
    const L_INFO            =       2;

    /**
     * 写入日志文件
     *
     * @param string $message
     * @param int $type
     * @return void
     */
    public static function writeToLog(string $message, int $type = self::L_ERROR) {
        if($type > LOG_LEVEL) {
            return;
        }

        switch($type) {
            case self::L_ERROR:
                @file_put_contents(ERROR_LOG_FILE, "[ERROR] " . $message, FILE_APPEND);
                break;
            case self::L_WARNING:
                @file_put_contents(WARNING_LOG_FILE, "[WARNING] " . $message, FILE_APPEND);
                break;
            case self::L_INFO:
                @file_put_contents(ACCESS_LOG_FILE, "[INFO] " . $message, FILE_APPEND);
        }
    }
}