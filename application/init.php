<?php

error_reporting(E_ALL ^E_NOTICE);
date_default_timezone_set('Asia/Chongqing');

define('CUR_DATE',      date('Y-m-d'));
define('CUR_DATETIME',  date('Y-m-d H:i:s'));
define('CUR_TIMESTAMP', time());

define('ENV', strtoupper(ini_get('yaf.environ')));

switch(ENV) {
    case 'DEV':
        ini_set('display_errors', 'on');

        $SERVER_DOMAIN = 'http://dev.yof.com';
        $STATIC_DOMAIN = 'http://devStatic.yof.com';
        $IMG_DOMAIN    = 'http://devImg.yof.com';
    break;

    case 'TEST':
        $logFile = APP_PATH.'/log/php/'.CUR_DATE.'.log';

        ini_set('display_errors', 'off');
        ini_set('log_errors', 'on');
        ini_set('error_log', $logFile);

        $SERVER_DOMAIN = 'http://test.yof.com';
        $STATIC_DOMAIN = 'http://testStatic.yof.com';
        $IMG_DOMAIN    = 'http://testImg.yof.com';
    break;

    case 'PRODUCT':
        $logFile = APP_PATH.'/log/php/'.CUR_DATE.'.log';

        ini_set('display_errors', 'off');
        ini_set('log_errors', 'on');
        ini_set('error_log', $logFile);

        $SERVER_DOMAIN = 'http://yof.mylinuxer.com';
        $STATIC_DOMAIN = 'http://static.yof.com';
        $IMG_DOMAIN    = 'http://img.yof.com';
    break;
}

// 以下为需要 define 的常量
define('YOF_VERSION',  '2.1'); // YOF VERSION
define('TB_PK',        'id');  // 表的主键, 用于 SelectByID 等
define('TB_PREFIX',    'zt_'); // 表前缀
define('APP_NAME',     'YOF-DEMO'); // APP 名称
define('LIB_PATH',     APP_PATH.'/application/library');
define('CORE_PATH',    LIB_PATH.'/core');
define('MODEL_PATH',   APP_PATH.'/application/model');
define('FUNC_PATH',    APP_PATH.'/application/function');
define('ADMIN_PATH',   APP_PATH.'/application/modules/Admin');

// COMMON_PATH
define('COMMON_PATH', APP_PATH.'/public/common');

// API KEY for api sign
define('API_KEY', 'THIS_is_OUR_API_keY');

// CSS, JS, IMG PATH
define('CSS_PATH', '/css');
define('JS_PATH',  '/js');
define('IMG_PATH', '/img');

// Admin CSS, JS PATH
define('ADMIN_ASSET',  '/admin/assets');
define('ADMIN_CSS_PATH', '/admin/assets/css');
define('ADMIN_JS_PATH',  '/admin/assets/js');

// PHP log file
define('LOG_FILE', $logFile);

// domains for server, static, img
define('SERVER_DOMAIN', $SERVER_DOMAIN);
define('STATIC_DOMAIN', $STATIC_DOMAIN);
define('IMG_DOMAIN',    $IMG_DOMAIN);

define('SITE_PROVINCE', 440000);
define('SITE_CITY',     440100);
define('SITE_REGION',   440106);

// tmp path and upload path
define('TMP_PATH',    APP_PATH.'/tmp');
define('UPLOAD_PATH', APP_PATH.'/public/upload');

// Super admin account
define('SUPER_ADMIN', 'superAdmin');

/*
 * DEV 下我们使用自定义输出错误, 这样能更好的 debug
 * PRODUCT 下则报 500, 记录错误至指定日志
 * 注: 由于这些不能输出至 html, 使用了比较恶心的处理方式. 
 *     若有更好办法, 请告知, 谢谢!
 */
function yofErrorHandler($errno, $errstr, $errfile, $errline, $sql = ''){
    if(ENV != 'DEV'){
        file_put_contents(LOG_FILE, CUR_DATETIME.' '.$errno.PHP_EOL,   FILE_APPEND);
        file_put_contents(LOG_FILE, CUR_DATETIME.' '.$errstr.PHP_EOL,  FILE_APPEND);
        file_put_contents(LOG_FILE, CUR_DATETIME.' '.$errfile.PHP_EOL, FILE_APPEND);
        file_put_contents(LOG_FILE, CUR_DATETIME.' '.$errline.PHP_EOL, FILE_APPEND);
        
        header('HTTP/1.1 500 Internal Server Error');
        $html = '<html>
            <head><title>500 Internal Server Error</title></head>
            <body bgcolor="white">
            <center><h1>500 Internal Server Error</h1></center>
            <hr>
            </body>
        </html>';
        die($html);
    }else{
        $error = '<link href="CSS_PATH/bootstrap.min.css" rel="stylesheet">
            <link href="CSS_PATH/bootstrap-responsive.min.css" rel="stylesheet">
            <link href="CSS_PATH/docs.css" rel="stylesheet">
            <script src="JS_PATH/jquery-1.7.min.js"></script>';
        
        $error .= '<style>
                body{
                        font-family:"ff-tisa-web-pro-1","ff-tisa-web-pro-2","Lucida Grande","Helvetica Neue",Helvetica,Arial,"Hiragino Sans GB","Hiragino Sans GB W3","Microsoft YaHei UI","Microsoft YaHei","WenQuanYi Micro Hei",sans-serif;
                        padding: 10px;
                    }
                </style>';
        $error .= '<link href="CSS_PATH/prettify.css" rel="stylesheet">';
        $error .= "<script> 
                  $(function(){
                    $('#errorTab a').click(function(e){
                        e.preventDefault();
                        $('#errorTab a').parent().removeClass('active'); 
                        $(this).parent().addClass('active');

                        // 切换 DIV
                        $('.tab-content div').removeClass('active');
                        var id = $(this).attr('val');
                        $('#'+id).addClass('active');
                    }) 
                  }) 
                </script>";
        $error .= '<h4>Error : [ERROR_DESC]</h4>
                    <ul class="nav nav-tabs" id="errorTab"> 
                      <li class="active"><a val="general" href="#general">General</a></li> 
                      <li><a val="request" href="#request">Request</a></li> 
                      <li><a val="router" href="#router">Router</a></li>
                      <li><a val="modules" href="#modules">Modules</a></li>
                      <li><a val="config" href="#config">Config</a></li> 
                      <li><a val="get" href="#get">GET</a></li>
                      <li><a val="post" href="#post">POST</a></li> 
                      <li><a val="cookie" href="#cookie">COOKIE</a></li> 
                      <li><a val="session" href="#session">SESSION</a></li> 
                      <li><a val="server" href="#server">SERVER</a></li>
                      <li><a val="sql" href="#sql">SQL</a></li>
                    </ul>';

        $error .= '<div class="tab-content">
                      <div class="tab-pane active" id="general">[GENERAL_ERR]</div> 
                      <div class="tab-pane" id="request">[REQUEST_ERR]</div> 
                      <div class="tab-pane" id="router">[ROUTER_ERR]</div> 
                      <div class="tab-pane" id="modules">[MODULES_ERR]</div>
                      <div class="tab-pane" id="config">[CONFIG_ERR]</div>
                      <div class="tab-pane" id="get">[GET_ERR]</div>
                      <div class="tab-pane" id="post">[POST_ERR]</div>
                      <div class="tab-pane" id="cookie">[COOKIE_ERR]</div>
                      <div class="tab-pane" id="session">[SESSION_ERR]</div>
                      <div class="tab-pane" id="server">[SERVER_ERR]</div>
                      <div class="tab-pane" id="sql">[SQL_ERR]</div>
                    </div>';

        $search  = array('CSS_PATH', 'JS_PATH');
        $replace = array(CSS_PATH, JS_PATH);
        $error = str_replace($search, $replace, $error);

        // Environ
        $environ = Yaf_Application::app()->environ();

        // General
        $generalErr = '<ul>';
        $generalErr .= '<li>Environ: '.$environ.'</li>';
        $generalErr .= '<li>Error NO: '.$errno.'</li>';
        $generalErr .= '<li>Error: '.$errstr.'</li>';
        $generalErr .= '<li>File: '.$errfile.'</li>';
        $generalErr .= '<li>Line: '.$errline.'</li>';
        $generalErr .= '<li>URL: http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'</li>';
        $generalErr .= '</ul>';

        $error = str_replace('[ERROR_DESC]',  $errstr, $error);
        $error = str_replace('[GENERAL_ERR]', $generalErr, $error);

        // Request
        $request = Yaf_Dispatcher::getInstance()->getRequest();
        $requestErr = '<ul>';
        $requestErr .= '<li>Module: '.$request->module.'</li>';
        $requestErr .= '<li>Controller: '.$request->controller.'</li>';
        $requestErr .= '<li>Action: '.$request->action.'</li>';
        $requestErr .= '<li>URI: '.$request->getRequestUri().'</li>';
        $requestErr .= '</ul>';

        $error = str_replace('[REQUEST_ERR]', $requestErr, $error);

        // Routers
        $router = Yaf_Dispatcher::getInstance()->getRouter();
        $routers = $router->getRoutes();

        // TODO: Convert each route to array !
        // if($routers){
        //     Helper::import('Array');
        //     foreach($routers as $key => $val){
        //         //$val = array($val);
        //         //pr($key);
        //         pr($val); continue;
        //     }
        // }

        // Current Router
        $currentRouter = $router->getCurrentRoute();
        $routerErr = '<ul>';
        $routerErr .= '<li>Current Router: '.$currentRouter.'</li>';
        $routerErr .= '</ul>';
        $error = str_replace('[ROUTER_ERR]', $routerErr, $error);

        // Modules
        $modules = Yaf_Application::app()->getModules();

        $moduleErr = '<ul>';
        foreach($modules as $val){
            $moduleErr .= '<li>'.$val.'</li>';
        }
        $moduleErr .= '</ul>';

        $error = str_replace('[MODULES_ERR]', $moduleErr, $error);

        // Config
        $config = Yaf_Application::app()->getConfig();
        $configErr = '<ul>';
        foreach($config as $key => $val){
            if($key != 'application'){
                // Hide PSWD of MySQL
                if(strpos($key, 'PSWD') !== FALSE){
                    $val = '******';
                }   
                $configErr .= '<li>'.$key. ' => '.$val.'</li>';
            }
        }
        $configErr .= '</ul>';
        $error = str_replace('[CONFIG_ERR]', $configErr, $error);

        // $_GET
        $getErr = '<ul>';
        foreach($_GET as $key => $val){
            $getErr .= '<li>'.$key. ' => '.$val.'</li>';
        }
        $getErr .= '</ul>';
        $error = str_replace('[GET_ERR]', $getErr, $error);

        // $_POST
        $postErr = '<ul>';
        foreach($_POST as $key => $val){
            $postErr .= '<li>'.$key. ' => '.$val.'</li>';
        }
        $postErr .= '</ul>';
        $error = str_replace('[POST_ERR]', $postErr, $error);

        // $_COOKIE
        $cookieErr = '<ul>';
        foreach($_COOKIE as $key => $val){
            $cookieErr .= '<li>'.$key. ' => '.$val.'</li>';
        }
        $cookieErr .= '</ul>';
        $error = str_replace('[COOKIE_ERR]', $cookieErr, $error);

        // $_SESSION
        $sessionErr = '<ul>';
        if($_SESSION){
            foreach($_SESSION as $key => $val){
                $sessionErr .= '<li>'.$key. ' => '.$val.'</li>';
            }
        }
        $sessionErr .= '</ul>';
        $error = str_replace('[SESSION_ERR]', $sessionErr, $error);

        // $_SERVER
        $serveErr = '<ul>';
        foreach($_SERVER as $key => $val){
            $serveErr .= '<li>'.$key. ' => '.$val.'</li>';
        }
        $serveErr .= '</ul>';
        $error = str_replace('[SERVER_ERR]', $serveErr, $error);

        // SQL
        if($sql){
            $sqlErr = '<ul>';
            $sqlErr .= '<li>'.$sql.'</li>';
            $sqlErr .= '</ul>';
            $error = str_replace('[SQL_ERR]', $sqlErr, $error);
        }else{
            $error = str_replace('[SQL_ERR]', '', $error);
        }

        echo $error; die;
    }
}