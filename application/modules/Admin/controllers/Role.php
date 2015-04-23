<?php

class RoleController extends Yaf_Controller_Abstract {
	
	private $m_role  = null;
	private $request = null;
  	private $session = null;

	private function init(){
            $this->m_role  = Helper::load('Role');
            $this->request = $this->getRequest();
            $this->session = Yaf_Session::getInstance();
            include ADMIN_PATH.'/checkAdminLogin.php';

            $this->homeUrl = '/admin/role';
	}

	
	/**
	 *  Index : list all roles
	 */
	public function indexAction(){
		$page = $this->request->get('page');
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
		$mapping['username'] = $this->request->getPost('name');
		$mapping['password'] = md5($this->request->getPost('password'));
		$mapping['alias']    = $this->request->getPost('alias');
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
		$buffer['roleID'] = $this->request->get('roleID');
		$buffer['role'] = $this->m_role->SelectByID('', $buffer['roleID']);
		$this->getView()->assign($buffer);
	}
	
	
	/**
	 * Edit role action
	 */
	public function editActAction(){
		$id = $this->request->getPost('roleID');
		$m['username'] = $this->request->getPost('name');
		$m['alias']    = $this->request->getPost('alias');
		$resetPassword = $this->request->getPost('newPassword');
		
		// 选择了修改密码
		if('on' == $resetPassword){
			$m['password'] = md5($this->request->getPost('password'));
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
		$roleID = $this->request->get('roleID');
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
		$buffer['roleID'] = $this->request->get('roleID');
		
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
		 
		$roleID = $this->request->getPost('roleID');
		$privilege = implode(',', $finalArr);

		$data = $this->m_role->updatePrivilegeByRoleID($roleID, $privilege);
		
		if($data === FALSE){
			jsAlert('更新权限失败');
		}
		
		jsRedirect($this->homeUrl);
	}

}