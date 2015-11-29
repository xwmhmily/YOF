<?php

class RegisterController extends BasicController {

	private $m_user;

	private function init(){
		$this->m_user = $this->load('user');
		$userID = $this->getSession('userID');

		if($userID){
			jsRedirect('/user/profile');
		}
	}

	public function indexAction() {
        
  	}
  	
  	public function registerActAction(){
		$m['username'] = $this->getParam('username');
		$m['password'] = $this->getParam('password');

		if(!$m['username'] || !$m['password']){
			$error = 'Username and password are required !';
			$this->showError($error, 'index');
		}

		// Username exists ?
		$where = array('username' => $m['username']);
		$num = $this->m_user->Where($where)->Total();
		if($num){
			$msg = '注册名已存在, 请更换';
			$this->showError($msg, 'index');
		}
		
		$userID = $this->m_user->Insert($m);
		if(!$userID){
			$error = '注册失败,请重试';
			$this->showError($error, 'index');
		}else{
			$msg = '注册成功,请登录';
			$url = '/login';
		}

		jsAlert($msg);
		jsRedirect($url);
	}

}
