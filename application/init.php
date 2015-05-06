<?php

require APP_PATH.'/application/environment.php';

date_default_timezone_set('Asia/Chongqing');
define('CUR_DATE', date('Y-m-d'));
define('CUR_TIMESTAMP', time());

switch(strtoupper(ENVIRONMENT)) {
    case 'DEV':
        error_reporting(E_ALL ^E_NOTICE);
        ini_set('display_errors', 'on');

        $SERVER_DOMAIN = 'http://dev.yof.com';
        //$STATIC_DOMAIN = 'http://devStatic.yof.com';
        $STATIC_DOMAIN = '';
        $IMG_DOMAIN    = 'http://devImg.yof.com';
    break;

    case 'TEST':
        error_reporting(E_ALL ^E_NOTICE);
        $logFile = APP_PATH.'/log/php/'.CUR_DATE.'.log';

        if(!file_exists($logFile)){
            touch($logFile);
        }

        ini_set('display_errors', 'off');
        ini_set('log_errors', 'on');
        ini_set('error_log', $logFile);

        $SERVER_DOMAIN = 'http://test.yof.com';
        $STATIC_DOMAIN = 'http://testStatic.yof.com';
        $IMG_DOMAIN    = 'http://testImg.yof.com';
    break;

    case 'WWW':
        error_reporting(E_ALL ^E_NOTICE);
        $logFile = APP_PATH.'/log/php/'.CUR_DATE.'.log';

        if(!file_exists($logFile)){
            touch($logFile);
        }

        ini_set('display_errors', 'off');
        ini_set('log_errors', 'on');
        ini_set('error_log', $logFile);

        $SERVER_DOMAIN = 'http://www.yof.com';
        $STATIC_DOMAIN = 'http://static.yof.com';
        $IMG_DOMAIN    = 'http://img.yof.com';
    break;

    case 'MAINTAINCE':
        echo '<H2>服务器正在维护, 请稍候访问</h2>'; die;
    break;
}

define('LOG_FILE', $logFile);

define('SERVER_DOMAIN', $SERVER_DOMAIN);
define('STATIC_DOMAIN', $STATIC_DOMAIN);
define('IMG_DOMAIN',    $IMG_DOMAIN);

define('SITE_PROVINCE', 440000);
define('SITE_CITY',     440100);
define('SITE_REGION',   440106);
