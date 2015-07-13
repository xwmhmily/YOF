<?php

class RouterPlugin extends Yaf_Plugin_Abstract {

    public function routerStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {

    }

    // 去掉 Module 后的 index
    public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
        //pr($request);
        
        $modules = Yaf_Application::app()->getModules();

    	$uri = $request->getRequestUri();
    	$uriInfo = explode('/', $uri);

    	$module = ucfirst($uriInfo[1]);

    	if(!in_array($module, $modules)){
            $module = 'index';
            // 由于 YAF 源码只不支持大小写混写的控制器和 Action名, 这里来满足
            if($request->controller){
                if(strtoupper($request->controller) == strtoupper($uriInfo[1])){
                    $controller = ucfirst($uriInfo[1]);
                    $request->setControllerName($controller);
                }
            }

            if($request->action){
                if(strtoupper($request->action) == strtoupper($uriInfo[2])){
                    $request->setActionName($uriInfo[2]);
                }
            }
    	}else{
            $request->setModuleName($module);
            $request->setControllerName(ucfirst($uriInfo[2]));

            $action = $uriInfo[3];
            if(!$action){
                $action = 'index';
            }

            $request->setActionName($action);
        }

	   //pr($request);
    }

}