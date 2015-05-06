<?php

class Bootstrap extends Yaf_Bootstrap_Abstract{

    // Init config
    public function _initConfig() {
        $config = Yaf_Application::app()->getConfig();
        Yaf_Registry::set('config', $config);
    }

    // Load libaray, MySQL model, function
    public function _initCore() {
        define('TB_PREFIX',    'zt_');
        define('APP_NAME'   ,  'YOF-DEMO');
        define('CONFIG_PATH',  APP_PATH.'/conf');
        define('LIB_PATH',     APP_PATH.'/application/library');
        define('MODEL_PATH',   APP_PATH.'/application/model');
        define('FUNC_PATH',    APP_PATH.'/application/function');
        define('ADMIN_PATH',   APP_PATH.'/application/modules/Admin');

        // CSS, JS, IMG PATH
        define('CSS_PATH', '/css');
        define('JS_PATH',  '/js');
        define('IMG_PATH',  '/img');

        // Admin CSS, JS PATH
        define('ADMIN_CSS_PATH', '/admin/css');
        define('ADMIN_JS_PATH',  '/admin/js');

        Yaf_Loader::import('M_Model.pdo.php');
        Yaf_Loader::import('Helper.class.php');

        Helper::import('Basic');
        Helper::import('Network');
        
        Yaf_Loader::import('C_Basic.php');
    }


    public function _initRoute() {
        $router = Yaf_Dispatcher::getInstance()->getRouter();

        // Article detail router [伪静态]
        $route = new Yaf_Route_Rewrite(
            '/article/detail/:articleID',
            array(
                'controller' => 'article',
                'action'     => 'detail',
            )
        );

        $router->addRoute('regex', $route);
    }

    public function _initPlugin(Yaf_Dispatcher $dispatcher) {
        $router = new RouterPlugin();
        $dispatcher->registerPlugin($router);

        $admin = new AdminPlugin();
        $dispatcher->registerPlugin($admin);
        Yaf_Registry::set('adminPlugin', $admin);
    }

}
