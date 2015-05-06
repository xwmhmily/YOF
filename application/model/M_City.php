<?php
/**
 *  File: M_City.class.php
 *  Functionality: City model class
 *  Author: Nic XIE
 *  Date: 2013-1-27
 */

class M_City extends M_Model {

    function __construct() {
        $this->table = TB_PREFIX.'city';
        parent::__construct();
    }
	
	
	/**
	  * 通过城市ID 查找城市名称
	  *@param $cityID: 城市ID
	  * @return 城市名称
	  */ 
	public function getCityNameById($cityID){
		$field = array('city');
		$where = array('cityID' => $cityID);
		$data  = $this->Field($field)->Where($where)->SelectOne();
		return $data['city'];
	}
	
	
	/**
	 * 取出指定省份的所有城市
	 * 
	 * @param $provinceID: 省份ID
	 * @return 所属省份的所有城市信息
	 */ 
    public function getCityByProvinceId($provinceID){
    	$field = array('cityID', 'city');
        $where = array('provinceID' => $provinceID);
		return $this->Field($field)->Where($where)->Select();
    }
	
}