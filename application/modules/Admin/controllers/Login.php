<?php

class LoginController extends BasicController {

	private $m_role;
	private $m_admin;

	private function init(){
		session_start();
		$this->m_admin = $this->load('Admin');
		$this->m_role  = $this->load('Role');

		$this->homeUrl = '/admin/login';
	}

	public function indexAction(){
		
	}

	public function checkLoginAction(){
		$username = $this->getPost('username');
		$password = $this->getPost('password');
		$captcha  = $this->getPost('captcha');
		
		if(!$username || !$password){
			jsAlert('信息不完整!');
			jsRedirect($this->homeUrl);

			if(ENV != 'DEV'){
				if(!$captcha){
					jsAlert('信息不完整!');
					jsRedirect($this->homeUrl);
				}
			}
		}

		if(ENV != 'DEV'){
			if(strtolower($captcha) != strtolower($_SESSION['adminCaptcha'])){
				jsAlert('验证码不正确!');
				jsRedirect($this->homeUrl);
			}
		}
		
		// 管理员登陆
		if(SUPER_ADMIN == $username){
			$data = $this->m_admin->checkLogin($username, $password);
		}else{
			// 普通角色登陆
			$data = $this->m_role->checkRole($username, $password);
		}

		if(!$data){
			// Login fail
			$log['status'] = 0;
			jsAlert('账号或密码不正确!');
			jsRedirect($this->homeUrl);
		}else{
			// Login OK, log this action and find privileges
			$this->setSession('adminID', $data['id']);
			$this->setSession('adminName', $username);
			
			// admin 拥有所有的权限
			if(SUPER_ADMIN == $username){
				$this->setSession('priv', 'ALL');
			}else{
				// 不是管理员, 记录其 roleID, 用于查找权限
				// 1: 取得登录的角色所拥有的权限
				$priv = $this->m_role->getPrivilegeByRoleID($data['id']);
				
				// 如果角色没有分配到任何权限, 提示, 并且退出, 有则 SESSION 记录其所有的权限，供进一步处理
				if(!$priv['privilege']){
					jsAlert('您还没有任何权限, 请联系管理员!');
					jsRedirect($this->homeUrl);
				}else{
					$this->setSession('priv', $priv);
				}
			}
		}

		// Privileges
		include APP_PATH.'/application/modules/Admin/menu.php';
		if(SUPER_ADMIN != $this->getSession('adminName')){
			$priv = $this->getSession('priv');
			$priv = explode(',', $priv['privilege']);
			
			// 1: 与大菜单对比, 删除会员没有权限的菜单
			foreach($menu as $k => $v){
				foreach($v as $kk => $vv){
					if(is_array($vv)){
						foreach($vv as $kkk => $vvv){
							if(!in_array($kkk, $priv)){
								unset($menu[$k][$kk][$kkk]);
							}
						}
					}
				}
			}
			
			// 2: 进一步处理: 删除没有子菜单的项
			foreach($menu as $k => $v){
				if(!$v['sub']){
					unset($menu[$k]);
				}
			}
		}

		$this->setSession('menu', $menu);
		jsRedirect('/admin/dashboard');
	}

	public function logoutAction(){
		$this->unsetSession('adminID');
		$this->unsetSession('adminName');

		$this->goHome();
	}
}