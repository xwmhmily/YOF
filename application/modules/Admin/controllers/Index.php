<?php

class IndexController extends Yaf_Controller_Abstract {

	private $request = null;
  	private $session = null;
  	private $adminAccount = 'superAdmin';

	private function init(){
		$this->request = $this->getRequest();
		$this->session = Yaf_Session::getInstance();
	}

	public function mainAction(){
		include APP_PATH.'/application/modules/Admin/menu.php';
		if($this->adminAccount != $this->session->__get('adminName')){
			$priv = $this->session->__get('priv');
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

		$buffer['menu'] = $menu;
		$this->session->__set('menu', $menu);
		$this->getView()->assign($buffer);
	}

	public function resetAction(){

	}

	public function resetActAction(){
		$t = $this->request->getPost('t');
		$m = array();
		$m['password'] = md5($t['newPass']);
		$where = ' where `username` = "' . $_SESSION['adminName'] . '" LIMIT 1';
		
		if($this->adminAccount == $_SESSION['adminName']){
			$data = Helper::load('Admin')->Update($m, $where);
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