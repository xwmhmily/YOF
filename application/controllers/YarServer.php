<?php
/**
 *  Yar server
 */

class YarServerController extends BasicController {

	private function init(){
		// We don't need view in server mode
		Yaf_Dispatcher::getInstance()->disableView();
	}

	public function articleAction() {
		Yaf_Loader::import(LIB_PATH.'/yar/Yar_Article.php');
		
		$service = new Yar_Server(new Yar_Article());
        $service->handle();
	}

	public function userAction() {
		Yaf_Loader::import(LIB_PATH.'/yar/Yar_User.php');
		
		$service = new Yar_Server(new Yar_User());
        $service->handle();
	}
  	
}
