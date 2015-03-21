<?php
/**
 *  File: M_Region.class.php
 *  Functionality: Region model class
 *  Author: Nic XIE
 *  Date: 2013-3-24
 */

class M_Region extends M_Model {

    function __construct() {
        $this->table = TB_PREFIX.'region';
        parent::__construct();
    }

    
    /**
     * 根据区域ID 查找区域名称
     * @param $provinceID: 区域ID
	 * @return 区域名称
     */
	public function getRegionNameById($regionID){
		$field = array('region');
		$where = array('regionID' => $regionID);
		$data  = $this->Field($field)->Where($where)->SelectOne();
		return $data['region'];
	}
	
	
	/**
	 * 根据区域ID 查找所在的城市ID
	 * 
	 * @param $provinceID: 区域ID
	 * @return 城市ID
	 */
	public function getCityIdByRegionID($regionID){
		$field = array('cityID');
		$where = array('regionID' => $regionID);
		$data  = $this->Field($field)->Where($where)->SelectOne();

		return $data['cityID'];
	}
	
	
	/**
	 * 取出指定城市下的所有区域
	 * @param int $cityID: 城市ID
	 * @param string $fields: 要获取的列
	 * @return 所属城市的所有区域信息
	 */ 
    public function getRegionByCityId($cityID){
    	$field = array('regionID','region');
		$where = array('cityID' => $cityID);
        return $this->Field($field)->Where($where)->Select();
    }
	
}