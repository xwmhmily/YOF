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
		$page = $this->get('page');
		$page = isset($page) ? $page : 1;
		
		$total = $this->m_role->Total();
		
		$limit = 15;
		$totalPages = ceil($total / $limit);
		
		$page = verifyPage($page, $totalPages);
		
		$start = ($page - 1) * $limit;
		$buffer['pageNav'] = generatePageLink($page, $totalPages, $this->indexURL, $total);
		$order = array('id' => 'DESC');
		$limit = " $start, $limit";
		$buffer['roles'] = $this->m_role->Order($order)->Limit($limit)->Select();
		
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
		$mapping['username'] = $this->getPost('name');
		$mapping['password'] = md5($this->getPost('password'));
		$mapping['alias']    = $this->getPost('alias');
		$mapping['addTime']  = CUR_TIMESTAMP;
		
		$id = $this->m_role->Insert($mapping);
		
		if(!$id){
			jsAlert('添加失败, 请重试');
		}
		
		jsRedirect($this->homeUrl);
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
		$id = $this->getPost('roleID');
		$m['username'] = $this->getPost('name');
		$m['alias']    = $this->getPost('alias');
		$resetPassword = $this->getPost('newPassword');
		
		// 选择了修改密码
		if('on' == $resetPassword){
			$m['password'] = md5($this->getPost('password'));
		}
		
		$result = $this->m_role->UpdateByID($m, $id);
		
		if($result === FALSE) {
		    jsAlert('添加失败, 请重试');
		}
		
		jsRedirect($this->homeUrl);
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
		
		jsRedirect($this->homeUrl);
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
		
		jsRedirect($this->homeUrl);
	}

}