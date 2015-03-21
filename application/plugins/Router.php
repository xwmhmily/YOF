<?php

class RouterPlugin extends Yaf_Plugin_Abstract {

    public function routerStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {

    }

    // 去掉另了 Module 后的 index
    public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
    	//pr($request);

        $modules = Yaf_Application::app()->getModules();

    	$uri = $request->getRequestUri();
    	$uriInfo = explode('/', $uri);

    	$module     = ucfirst($uriInfo[1]);
    	$controller = $uriInfo[2];
    	$action     = $uriInfo[3];

    	if(!in_array($module, $modules)){
    		$module = 'index';
    		$controller = $uriInfo[1];
    		$action     = $uriInfo[2];
    	}

    	$request->setModuleName(ucfirst($module));

    	if(!$controller){
    		$controller = 'index';
    	}

    	$request->setControllerName(ucfirst($controller));

    	if(!$action){
    		$action = 'index';
    	}

    	$request->setActionName($action);

	    //pr($request);
    }

}