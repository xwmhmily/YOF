<?php
/*
 * CLI 的入口文件
 * CLI 下一般用于执行定时脚本或任务
 */

header('content-Type:text/html;charset=utf-8;');
define('APP_PATH',  realpath(dirname(__FILE__) . '/../')); 

Yaf_Loader::import(APP_PATH.'/application/init.php');

/*
 * callback 为回调函数, $argc 为参数总数, $argv 为可变的参数列表
 * 可根据 $argv 来作不同的业务处理
 * callback 中模型的加载请使用 Helper::load($model), 其余的和 WEB 一样
 */

$app = new Yaf_Application(APP_PATH.'/conf/application.ini');
$app->bootstrap()->execute('callback', $argc, $argv);

/*
 * 示例: 根据用户 ID 查询用户资料, 命令如下
 * php cli.php 9
 */
function callback($argc, $argv) {
	$m_user = Helper::load('User');
	$field  = array('id', 'username', 'realname', 'province', 'city', 'region');
	$users  = $m_user->SelectByID($field, $argv[1]);
	pr($users);
}
