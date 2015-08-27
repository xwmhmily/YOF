<?php
/**
 * File: M_Role.php
 * Functionality: Role model
 * Author: Nic XIE
 * Date: 2013-5-8
 * Remark:
 */

class M_Role extends Model {

	function __construct() {
		$this->table = TB_PREFIX.'role';
		parent::__construct();
	}

	/**
	 * Check Role login 
	 *
	 * @param string $username
	 * @return string $password
	 * @return 1 on success or 0 or failure
	 */
	public function checkRole($username, $password){
		$field = array('id', 'alias', 'status');
		$where = array('username' => $username, 'password' => md5($password));

		return $this->Field($field)->Where($where)->SelectOne();
	}


	// 根据 roleID 获取权限
	public function getPrivilegeByRoleID($roleID){
		$field = 'privilege';
		return $this->SelectByID($field, $roleID);
	}
	

	// 更新 roleID 权限
	public function updatePrivilegeByRoleID($roleID, $privilege){	
		$m = array('privilege' => $privilege);
		return $this->UpdateByID($m, $roleID);
	}

}