<?php

class RoleController extends BasicController {
	
	private $m_role;

	private function init(){
		Yaf_Registry::get('adminPlugin')->checkLogin();
		
        $this->m_role  = $this->load('Role');
        $this->homeUrl = '/admin/role';
	}
	
	/**
	 *  Index : list all roles
	 */
	public function indexAction(){
		$keyword = $this->get('keyword');
		if($keyword){
			$query = '&keyword='.$keyword;
			$buffer['keyword'] = $keyword;
			$total = $this->m_role->Where('username', 'LIKE', $keyword)->Total();
		}else{
			$total = $this->m_role->Total();	
		}

		$totalPages = ceil($total / 10);
		
		$order = array('id' => 'DESC');
		$limit = $this->getLimit();

		$page = $this->get('page');
		$buffer['pageNav'] = generatePageLink($page, $totalPages, $this->homeUrl, $total, $query);
		
		if($keyword){
			$buffer['roles'] = $this->m_role->Where('username', 'LIKE', $keyword)->
									Order($order)->Limit($limit)->Select();
		}else{
			$buffer['roles'] = $this->m_role->Order($order)->Limit($limit)->Select();
		}
		
		$this->getView()->assign($buffer);
	}
	
	
	/**
	 * Add new role
	 */
	public function addAction(){

	}
	
	/**
	 * Add new role action
	 */
	public function addActAction(){
		$m['username'] = $this->getPost('name');
		$m['password'] = md5($this->getPost('password'));
		$m['alias']    = $this->getPost('alias');
		$m['addTime']  = CUR_TIMESTAMP;
		
		$id = $this->m_role->Insert($m);
		
		if(!$id){
			jsAlert('添加失败, 请重试');
		}
		
		$this->goHome();
	}
	
	
	/**
	 * Eidt a role
	 */
	public function editAction(){
		$buffer['roleID'] = $this->get('roleID');
		$buffer['role'] = $this->m_role->SelectByID('', $buffer['roleID']);
		$this->getView()->assign($buffer);
	}
	
	
	/**
	 * Edit role action
	 */
	public function editActAction(){
		$roleID        = $this->getPost('roleID');
		$m['username'] = $this->getPost('name');
		$m['alias']    = $this->getPost('alias');

		$status = $this->getPost('status');
		if($status == 'on'){
			$m['status'] = 1;
		}else{
			$m['status'] = 0;
		}
		
		// 选择了修改密码
		$password = $this->getPost('password');
		if($password){
			$m['password'] = md5($password);
		}
		
		$result = $this->m_role->UpdateByID($m, $roleID);
		
		if($result === FALSE) {
		    jsAlert('添加失败, 请重试');
		}
		
		$this->goHome();
	}
	
	
	/**
	 * Delete
	 */
	public function deleteAction(){
		$roleID = $this->get('roleID');
		$result = $this->m_role->DeleteByID($roleID);
		
		if(!$result){
			jsAlert('删除失败');
		}
		
		$this->goHome();
	}

	// 批量删除 
	public function delBatchAction(){
		$str = $this->get('str');
		$str = explode(',', $str);

		if($str){
			$row = $this->m_role->Where('id', 'IN', $str)->Delete();

			if(!$row){
				jsAlert(OPP_FAILURE);
			}
		}

		$this->goHome();
	}
	
	
	/**
	 * Assign privilege
	 */
	public function assignAction(){
		include APP_PATH.'/application/modules/Admin/menu.php';
		$buffer['roleID'] = $this->get('roleID');
		
		$priv = $this->m_role->getPrivilegeByRoleID($buffer['roleID']);
		$priv = $priv['privilege'];
		
		$buffer['priv'] = explode(',', $priv);
		$buffer['menu'] = $menu;
		$this->getView()->assign($buffer);
	}
	
	
	/**
	 * Assign or update privilege action
	 */
	public function assignActAction(){
		// 1: 取得选择的权限
		$finalArr = array();
		foreach($_POST as $key => $val){
			if(is_array($val)){
				foreach($val as $k => $v){
					$finalArr[] = $v;
				}
			}
		}
		 
		$roleID = $this->getPost('roleID');
		$privilege = implode(',', $finalArr);

		$data = $this->m_role->updatePrivilegeByRoleID($roleID, $privilege);
		
		if($data === FALSE){
			jsAlert('更新权限失败');
		}
		
		$this->goHome();
	}

}