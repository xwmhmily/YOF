<?php

class AdminPlugin extends Yaf_Plugin_Abstract {

    public function checkLogin(){
        $adminID = Yaf_Session::getInstance()->__get('adminID');

        if(!$adminID){
            jsRedirect('/admin/login');
        }
    }

}