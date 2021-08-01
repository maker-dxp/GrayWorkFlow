<?php

class Widget_Error_Handle {
    private const ERROR_TABLE = array(
        E_ERROR             =>      'E_ERROR',
        E_WARNING           =>      'E_WARNING',
        E_PARSE             =>      'E_PARSE',
        E_NOTICE            =>      'E_NOTICE',
        E_CORE_ERROR        =>      'E_CORE_ERROR',
        E_CORE_WARNING      =>      'E_CORE_WARNING',
        E_COMPILE_ERROR     =>      'E_COMPILE_ERROR',
        E_COMPILE_WARNING   =>      'E_COMPILE_WARNING',
        E_USER_ERROR        =>      'E_USER_ERROR',
        E_USER_WARNING      =>      'E_USER_WARNING',
        E_USER_NOTICE       =>      'E_USER_NOTICE',
        E_STRICT            =>      'E_STRICT',
        E_RECOVERABLE_ERROR =>      'E_RECOVERABLE_ERROR',
        E_DEPRECATED        =>      'E_DEPRECATED',
        E_USER_DEPRECATED   =>      'E_USER_DEPRECATED',
        E_ALL               =>      'E_ALL'
    );

    /**
     * 异常处理
     *
     * @param Exception $e
     */
    public static function exceptionHandle($e) {
        $messages  = PHP_EOL;
        $messages .= "[Date]\t\t"   . date('Y-m-d H:i:s')           . PHP_EOL;
        $messages .= "[File]\t\t"   . $e->getFile()                     . PHP_EOL;
        $messages .= "[Line]\t\t"   . $e->getLine()                     . PHP_EOL;
        $messages .= "[Message]\t"  . $e->getMessage()                  . PHP_EOL;
        $messages .= "[Trace]\n"    . $e->getTraceAsString()            . PHP_EOL . PHP_EOL;
        Widget_Log::writeToLog($messages, Widget_log::L_ERROR);

        if(WIDGET_DEBUG) {
            Widget_Api_Response::sendResponse($e->getCode(), null, $e->getMessage(). PHP_EOL . $e->getTraceAsString());
        }

        if($e instanceof Zen_Route_Exception) {
            Widget_Api_Response::sendHttpStatus($e->getCode());
            Widget_Api_Response::sendResponse($e->getCode());
        }

        if($e instanceof Zen_Exception) {
            Widget_Api_Response::sendHttpStatus(500);
            Widget_Api_Response::sendResponse(500);
        }

        if($e instanceof Widget_Exception) {
            Widget_Api_Response::sendHttpStatus(500);
            Widget_Api_Response::sendResponse($e->getCode());
        }
    }

    /**
     * 错误处理
     *
     * @param int $level
     * @param string $message
     */
    public static function errorHandle(int $level, string $message, string $file, int $line) {
        $messages  = PHP_EOL;
        $messages .= "[Date]\t\t"   . date('Y-m-d H:i:s')           . PHP_EOL;
        $messages .= "[Level]\t\t"  . self::ERROR_TABLE[$level]         . PHP_EOL;
        $messages .= "[File]\t\t"    . ($file ?? 'unknown')                                 . PHP_EOL;
        $messages .= "[Line]\t\t"    . ($line ?? 'unknown')                                 . PHP_EOL;
        $messages .= "[Message]\t"  . $message                          . PHP_EOL . PHP_EOL;
        Widget_Log::writeToLog($messages);

        Widget_Api_Response::sendHttpStatus(500);
        if(WIDGET_DEBUG) {
            Widget_Api_Response::sendResponse($level, null, $messages);
        }else {
            Widget_Api_Response::sendResponse(500);
        }
    }

    /**
     * 致命错误处理
     */
    public static function fatalErrorHandle() {
        $data = error_get_last();
        $messages  = PHP_EOL;
        $messages .= "[Date]\t\t"    . date('Y-m-d H:i:s')                                      . PHP_EOL;
        $messages .= "[Type]\t\t"    . (self::ERROR_TABLE[$data['type']] ?? 'unknown')              . PHP_EOL;
        $messages .= "[File]\t\t"    . ($data['file'] ?? 'unknown')                                 . PHP_EOL;
        $messages .= "[Line]\t\t"    . ($data['line'] ?? 'unknown')                                 . PHP_EOL;
        $messages .= "[Message]\n"   . ($data['message'] ?? 'unknown. maybe uncaught exception.')   . PHP_EOL . PHP_EOL;
        Widget_Log::writeToLog($messages, Widget_log::L_ERROR);
    }
}