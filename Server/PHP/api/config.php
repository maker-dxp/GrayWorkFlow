<?php
const SERVER        =       'localhost';
const DB_USER       =       '';
const DB_PASSWD     =       '';
const DB_NAME       =       '';
const CHARSET       =       'utf8mb4';
//数据库配置

const WWWROOT       =       '';
define('WWWROOT_LENGTH', strlen(WWWROOT));
//网站根目录,不包含'/',如为根目录则留空

const APIROOT       =       '';
define('APIROOT', strlen(APIROOT));
//api目录相对路径,默认为api,可填入:api/不填

ini_set('date.timezone', 'Asia/Shanghai');
//时区设置,如有异常请注释掉

const DEBUG = true;
?>