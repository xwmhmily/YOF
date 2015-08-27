<?php
/**
 * File: M_Admin.php
 * Functionality: Admin model
 * Author: Nic XIE
 * Date: 2013-5-8
 * Remark:
 */

class M_Admin extends Model {

	function __construct() {
		$this->table = TB_PREFIX.'admin';
		parent::__construct();
	}

	/**
	 * Check admin login 
	 *
	 * @param string $username
	 * @return string $password
	 * @return 1 on success or 0 or failure
	 */
	public function checkLogin($username, $password){
		$field = array('id');
		$where = array('username' => $username, 'password' => md5($password));
		return $this->Field($field)->Where($where)->SelectOne();
	}
        
        // 查询文章列表
        public function getUserArticles($userID){
            $sql = 'SELECT u.username, a.* FROM '.TB_PREFIX.'user AS u '
                    . ' LEFT JOIN '.TB_PREFIX.'article AS a ON a.userID = u.id '
                    . ' WHERE a.userID = "'.$userID.'" ORDER BY a.addTime DESC LIMIT 10';
            
            return $this->Query($sql);
        }
}