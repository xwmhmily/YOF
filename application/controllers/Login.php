<?php

class LoginController extends BasicController {

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

  	public function loginActAction(){
		$username = $this->getPost('username');
		$password = $this->getPost('password');

		$field = array('id');
		$where = array('username' => $username, 'password' => $password);
		$data  = $this->m_user->Field($field)->Where($where)->SelectOne();
		$userID = $data['id'];

		if($userID){
			// Set to session
			$this->setSession('userID', $userID);
			$this->setSession('username', $username);

			echo 1;
		}else{
			echo 0;
		}

		die;
	}
  	
}
