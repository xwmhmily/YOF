<?php

// 如果有 xhprof 则开启跟踪功能
if(function_exists('xhprof_enable')){
	xhprof_enable(XHPROF_FLAGS_NO_BUILTINS | XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY);
}

header('content-Type:text/html;charset=utf-8;');
define('APP_PATH',  realpath(dirname(__FILE__) . '/../')); 

/*
 * 之所有要先包含 init.php 而不放在Bootstrap.php 里, 
 * 因为这样可以根据 ENV 来判断要不要抛出 YAF 本身的前置错误
 * 如配置出错了, 不包含 init.php 会一片空白, 包含了在 DEV 下则会抛出错误提示
 */
require APP_PATH.'/application/init.php';

$app = new Yaf_Application(APP_PATH.'/conf/application.ini');
$app->bootstrap()->run();