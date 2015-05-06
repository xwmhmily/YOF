<?php
/**
 *  File: M_Province.php
 *  Functionality: Province model class
 *  Author: LWC
 *  Date: 2013-06-03
 */

class M_Province extends M_Model{
	
	function __construct(){
		$this->table = TB_PREFIX.'province';
		parent::__construct();
	}
	
	
	/**
	 * 通过省份ID 查找省份名称
	 * 
	 * @param $provinceID: 省份ID
	 * @return 省份名称
	 */
	public function getProvinceNameById($provinceID){
		$field = array('province');
		$where = array('provinceID' => $provinceID);
		$data  = $this->Field($field)->Where($where)->SelectOne();
		return $data['province'];
	}
	
	
	/**
	 * 取出省份信息
	 * 
	 * @param $where
	 * @return 省份中的 id, provinceID, province
	 */ 
    public function getProvinces($where = '') {
		$field = array('provinceID', 'province');
		return $this->Field($field)->Where($where)->Select();
    }
	
}