<?php

class AdminPlugin extends Yaf_Plugin_Abstract {

    public function routerStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {

    }

    public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
    	
    }

    public function checkLogin(){
        $adminID = Yaf_Session::getInstance()->__get('adminID');

        if(!$adminID){
            jsRedirect('/admin/login');
        }
    }

}