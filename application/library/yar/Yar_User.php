<?php

class Yar_User extends Yar_Basic {

	private $m_user;

	function __construct(){
		$this->verifySign();
		$this->m_user = Helper::load('User');
	}

	public function index(){
		$users = $this->m_user->Select();

		$rep['code'] = 1;
		$rep['users'] = $users;
		return $this->response($rep);
	}

	public function detail($userID){
		if(!$userID){
			return $this->error('ERR_MISSING');
		}

		$user = $this->m_user->SelectByID('', $userID);
		$rep['code'] = 1;
		$rep['user'] = $user;
		return $this->response($rep);
	}

}