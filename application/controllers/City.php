<?php
/**
 * File: C_City.php
 * Functionality: 省市区块三级联动中的AJAX控制器
 * Author: Nic XIE
 * Date: 2013-6-2
 * Remark:
 */

class CityController extends BasicController {
	
	// 生成的三级联动中 select 的名称与ID
	private $cityID     = 'areaCity';
	private $provinceID = 'areaProvince';
	private $regionID   = 'areaRegion';

	private $m_city     = null;
	private $m_region   = null;
	private $m_province = null;

	function init(){		
		$this->m_city     = $this->load('City');
		$this->m_region   = $this->load('Region');
		$this->m_province = $this->load('Province');
	}

	/**
	  * 城市区三级联动中的 AJAX 部分
	  */
	public function pcAjaxAction() {
		Helper::import('Array');
		$flag = $this->getQuery('flag');
		$targetID = $this->getQuery('targetID');

		switch($flag){
			case 'p':
				$function = 'getCityByProvinceId';
				$obj = $this->m_city;
			break;

			case 'c':
				$function = 'getRegionByCityId';
				$obj = $this->m_region;
			break;
		}

		$finalArr = $obj->$function($targetID);

		$finalStr = '';
		switch($flag){
			case 'p':
				$finalStr = "<select errorDiv=\"city_error\" tipsDiv=\"city_tips\" eleType='select' id='".$this->cityID."' name='".$this->cityID."' class='select_box selecttxt3' onchange='javascript:cityChange();' >";
				$finalStr .= "<option value=''>请选择城市</option>";

				if(isMultiArray($finalArr)){
					foreach($finalArr as $val){
						$finalStr .= "<option value=".$val['cityID'].">".$val['city']."</option>";
					}
				}else{
					$finalStr .= "<option value=".$finalArr['cityID'].">".$finalArr['city']."</option>";
				}

				$finalStr .= "</select>";
			break;

			case 'c';
				$finalStr = "<select errorDiv=\"region_error\" eleType='select' tipsDiv=\"city_tips\" id='".$this->regionID."' name='".$this->regionID."' class='select_box selecttxt3' onchange='javascript:regionChange();' data-required=\"true\">";
				$finalStr .= "<option value=''>请选择区域</option>";

				if(isMultiArray($finalArr)){
					foreach($finalArr as $val){
						$finalStr .= "<option value=".$val['regionID'].">".$val['region']."</option>";
					}
				}else{
					$finalStr .= "<option value=".$finalArr['regionID'].">".$finalArr['region']."</option>";
				}

				$finalStr .= "</select>";
			break;
		}

		echo $finalStr; die;
	}

}