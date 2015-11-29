<?php

class IndexController extends BasicController {

  	private $adminAccount = 'superAdmin';

	private function init(){
		Yaf_Registry::get('adminPlugin')->checkLogin();
	}

	public function resetAction(){
		
	}

	public function resetActAction(){
		$t = $this->getPost('t');
		$m = array();
		$m['password'] = md5($t['newPass']);
		
		if($this->adminAccount == $_SESSION['adminName']){
			$where = array('username' => $_SESSION['adminName']);
			$data  = $this->load('Admin')->Where($where)->UpdateOne($m);
		}
		
		if($data !== FALSE){
			unset($_SESSION['admin'], $_SESSION['priv'], $_SESSION['adminID'], $_SESSION['adminName']);
			$msg = '密码修改成功，请重新登录！';
			$url = '/admin/login';
		}else{
			$msg = '密码修改失败！';
			$url = '/admin/index/reset';  
		}
		
		jsAlert($msg);
		jsRedirect($url);
	}
}