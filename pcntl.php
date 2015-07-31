<?php
/**
 * 多进程采集
 * Author: 大眼猫
 * Remark: 请在 CLI 模式下运行!
 */

$final = array();
define('LIB_PATH', __DIR__.'/application/library');
include LIB_PATH.'/phpQuery/phpQuery.php';

function run($page){
	$destination = "http://www.ttpet.com/zixun/42/category-catid-42-$page.html";

	echo 'Crawling '.$destination."\n";
	phpQuery::newDocumentFile($destination);
	$articles = pq('#main_bg .zixunmain .p_lf .p_pad')->find('ul');

	foreach($articles as $article) {
	   	$m['title'] = pq($article)->find('dl dd a')->html();
		$final[] = $m;
	}

	echo '=========== Page ===========> '.$page."\r\n";
	print_r($final);
}

$page = 1;
$pid_arr = array();

// 开5个进程
while ($page <= 5){
	$pid = pcntl_fork();

	if ($pid){
		// 这里是父进程的代码
		$pid_arr[] = $pid;
	}else{
		// 这里是子进程的代码
		echo "子进程ID => ".posix_getpid()."\r\n";
		call_user_func('run', $page);
		exit(0);
	}
	$page++;
}

// 等待子进程退出
while(count($pid_arr) > 0) {
    $myID = pcntl_waitpid(-1, $status, WNOHANG);
    foreach($pid_arr as $key => $pid) {
        if($myID == $pid){
        	unset($pid_arr[$key]);
        } 
    }
    usleep(100);
}