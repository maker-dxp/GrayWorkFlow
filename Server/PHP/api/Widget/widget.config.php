<?php

const WIDGET_DEBUG                      =       true;

const CLASS_MAP                         =       array(
  'JWT'                                 =>      'Firebase/JWT/JWT.php',
  'JWK'                                 =>      'Firebase/JWT/JWK.php',
  'ExpiredException'                    =>      'Firebase/JWT/ExpiredException.php',
  'BeforeValidException'                =>      'Firebase/JWT/BeforeValidException.php',
  'SignatureInvalidException'           =>      'Firebase/JWT/SignatureInvalidException.php'
);

/*
 * 该数组的内容为重置路由时忽略的部分，写在这里的文件将会跳过检查
 */
const PHP_IGNORE                        =       array(
    'widget.config.php'
);

/* 日志文件位置 */
const ERROR_LOG_FILE                    =       '/var/log/gray/error.log';
const WARNING_LOG_FILE                  =       '/var/log/gray/error.log';
const ACCESS_LOG_FILE                   =       '/var/log/gray/access.log';
/* 日志级别 */
const LOG_LEVEL                         =       Widget_Log::L_ERROR;

//设置时区
date_default_timezone_set('Asia/Shanghai');