<?php

class UserController extends Yaf_Controller_Abstract {

	private $m_user  = null;
	private $request = null;
	private $session = null;

	private function init(){
		$this->request = $this->getRequest();
		$this->session = Yaf_Session::getInstance();

		$this->m_user = Helper::load('user');
		$userID = $this->session->__get('userID');

		if($userID){
			define('USER_ID', $userID);
		}
	}

	public function indexAction(){
		echo 'User index'; die;
	}

	// Login
	public function loginAction(){
		
	}

	public function loginActAction(){
		$username = $this->request->getPost('username');
		$password = $this->request->getPost('password');

		$field = array('id');
		$where = array('username' => $username, 'password' => $password);
		$data  = $this->m_user->Field($field)->Where($where)->SelectOne();
		$userID = $data['id'];

		if($userID){
			// Set to session
			$this->session->__set('userID', $userID);
			$this->session->__set('username', $username);

			$this->redirect('/'); // 会令 jsAlert失效
		}else{
			jsAlert('登录失败, 请检查用户名和密码');
			jsRedirect('/user/user/login');
		}
	}

	// Register
	public function registerAction(){

	}

	public function registerActAction(){
		$m['username'] = $this->request->getPost('username');
		$m['password'] = $this->request->getPost('password');
		
		$userID = $this->m_user->Insert($m);
		if(!$userID){
			$msg = '注册失败,请重试';
			$url = '/user/user/register';
		}else{
			$msg = '注册成功,请登录';
			$url = '/user/user/login';
		}

		jsAlert($msg);
		jsRedirect($url);
	}

	// Logout
	public function logoutAction(){
		$this->session->__unset('userID');
		$this->session->__unset('username');

		$this->redirect('/');
	}

	// Profile
	public function profileAction(){
		$buffer['user'] = $this->m_user->SelectByID('', USER_ID);

		$provinceID = $buffer['user']['provinceID'];
		$cityID = $buffer['user']['cityID'];
		$regionID = $buffer['user']['regionID'];

		$buffer['cityElement'] = Helper::loadComponment('City')->generateCityElement($provinceID, $cityID, $regionID, 1);
		$this->getView()->assign($buffer);
	}

	public function profileActAction(){
		$m['realname']   = $this->request->getPost('realname');
		$m['provinceID'] = $this->request->getPost('areaProvince');
		$m['cityID'] = $this->request->getPost('areaCity');
		$m['regionID'] = $this->request->getPost('areaRegion');

		$m['province'] = Helper::load('Province')->getProvinceNameByID($m['provinceID']);
		$m['city'] = Helper::load('City')->getCityNameByID($m['cityID']);
		if($m['regionID']){
			$m['region'] = Helper::load('Region')->getRegionNameByID($m['regionID']);
		}
		
		$code = $this->m_user->UpdateByID($m, USER_ID);
		if(!$code){
			jsAlert('编辑个人信息失败, 请重试');
			jsRedirect('/user/user/profile');
		}

		$this->redirect('/');
	}
}