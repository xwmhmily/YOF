<?php

class DashboardController extends BasicController {

	private function init(){
		Yaf_Registry::get('adminPlugin')->checkLogin();
	}

	public function indexAction(){
		$buffer['menu'] = $this->getSession('menu');
		$this->getView()->assign($buffer);
	}
}