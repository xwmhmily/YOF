<?php

class IndexController extends BasicController {

  	private $adminAccount = 'superAdmin';

	private function init(){
		
	}

	public function mainAction(){
		include APP_PATH.'/application/modules/Admin/menu.php';
		if($this->adminAccount != $this->getSession('adminName')){
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

		$buffer['menu'] = $menu;
		$this->setSession('menu', $menu);
		$this->getView()->assign($buffer);
	}

	public function resetAction(){

	}

	public function resetActAction(){
		$t = $this->getPost('t');
		$m = array();
		$m['password'] = md5($t['newPass']);
		$where = ' WHERE `username` = "' . $_SESSION['adminName'] . '" LIMIT 1';
		
		if($this->adminAccount == $_SESSION['adminName']){
			$data = $this->load('Admin')->Update($m, $where);
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