<?php
/**
 * File: City.php
 * Functionality: 省市区三级联动
 * Author: Nic XIE
 * Date: 2013-6-2
 * Remark:
 */

class City {

	// 生成的三级联动中 select 的名称与ID
	private $cityID     = 'areaCity';
	private $provinceID = 'areaProvince';
	private $regionID   = 'areaRegion';

	private $m_city;
	private $m_region;
	private $m_province;

	function __construct(){		
		$this->m_city     = Helper::load('City');
		$this->m_region   = Helper::load('Region');
		$this->m_province = Helper::load('Province');
	}


	/**
	 * 生成省市区块三级联动菜单
	 * 
	 * @param $PROVINCE 默认选中的省份
	 * @param $CITY     默认选中的城市
	 * @param $REGION   默认选中的区域
	 * @param $show    是否是显示区域, 默认 0  不显示, 1 显示
	 */ 
	public function generateCityElement($PROVINCE = SITE_PROVINCE, $CITY = SITE_CITY, $REGION = SITE_REGION, $show = 0) {
$str = <<< EOF
	<style type="text/css">
		select{padding-right:1px;}
	</style>
	<script type="text/javascript">
		$(function(){
			// 省份查城市
			$('#areaProvince').change(function(){
				var selectedProvinceID = $(this).val();
				if('' != selectedProvinceID){
					pcAjax(selectedProvinceID, 'p');
				}
			})
		})

		/**
		 *  Functionality: 三级联动
		 *  targetID: 省份ID 、 城市ID
		 *  flag: p 、 c 
		 */
		function pcAjax(targetID, flag){
			$.ajax({
				type : "get",
				url  : "/city/pcAjax/?r=&targetID=" + targetID + '&flag=' + flag,
				success:function(data){
					if('p' == flag){
						$('#cityDIV').html(data);
					}else if('c' == flag){
						$('#regionDIV').html(data);
					}
				}
			});
		}
EOF;

		// 如果不显示区域, 则不绑定 cityChange 此函数, 以便可以写其他业务
		if($show){
			$regionStr = "function cityChange(){
				// 城市查区域
				var selectedCityID = $('#areaCity').val();
				if('' != selectedCityID){
					pcAjax(selectedCityID, 'c');
				}
			}";

			$str .= $regionStr;
		}

		$str .= '</script>';

		//  ======  Step 1: 取得所有的省份, 组成 SELECT, ====== //
		$provinceArr = $this->m_province->getProvinces();

		$provinceSelect = "<span id='provinceDIV'><select eleType='select' id='".$this->provinceID."' name='".$this->provinceID."' class='selecttxt3'>";
		$provinceSelect .= '<option value="0">请选择省份</option>';

		foreach($provinceArr as $val){
			$provinceSelect .= "<option value=".$val['provinceID'];

			if($val['provinceID'] == $PROVINCE){
				$provinceSelect .= " selected = \"selected\" ";
			}

			$provinceSelect .= ">";
			$provinceSelect .= $val['province']."</option>";
		}
		$provinceSelect .= "</select></span>";


		// ======  Step 2: 取得选中省份的所有城市, 组成 SELECT ======  //
		$citySelect = "<span id='cityDIV'><select eleType='select' id='".$this->cityID."' name='".$this->cityID."' onchange='javascript:cityChange();'
		class='selecttxt3'>";
		$citySelect .= "<option value='0'>请选择城市</option>";

		$cityArr = '';
		if($PROVINCE){
			$cityArr = $this->m_city->getCityByProvinceId($PROVINCE);	

			foreach($cityArr as $val){
				$citySelect .= "<option value=".$val['cityID'];

				if($CITY && $val['cityID'] == $CITY){
					$citySelect .= " selected = \"selected\" ";
				}

				$citySelect .= ">";
				$citySelect .= $val['city']."</option>";
			}
		}

		$citySelect .= "</select></span>";

		// ====== Step 3: 取得城市中所有的区域 ============== //
		$regionSelect = "<span id='regionDIV'><select eleType='select' id='".$this->regionID."' name='".$this->regionID."' onchange='javascript:regionChange();'
		class='selecttxt3'>";
		$regionSelect .= "<option value='0'>请选择区域</option>";

		$regionArr = '';
		if($CITY){
			$regionArr = $this->m_region->getRegionByCityId($CITY);	

			foreach($regionArr as $val){
				$regionSelect .= "<option value=".$val['regionID'];

				if($REGION && $val['regionID'] == $REGION){
					$regionSelect .= " selected = \"selected\" ";
				}

				$regionSelect .= ">";
				$regionSelect .= $val['region']."</option>";
			}
		}

		$regionSelect .= "</select></span>";
		$str .= $provinceSelect .' '. $citySelect;

		if($show){
			$str .= ' '.$regionSelect;
		}
		return $str;
	}


	/*
	 * 生成层级式的省市区三级联动菜单
	 * 
	 * 层级式: 默认只出现省, 选择省份出现城市 SELECT, 选择城市出现区域 SELECT
	 * @param $PROVINCE 默认选中的省份
	 * @param $level 显示有级层 
			1 => 只显示省
			2 => 显示到市
			3 => 显示到区域
	 */ 
	public function generatePopCityElement($PROVINCE = SITE_PROVINCE, $level = 1) {
$str = <<< EOF
	<style type="text/css">
		select{padding-right:1px;}
	</style>
	<script type="text/javascript">
		$(function(){
			// 省份查城市
			$('#areaProvince').change(function(){
				var selectedProvinceID = $(this).val();
				if('' != selectedProvinceID){
					pcAjax(selectedProvinceID, 'p');
				}
			})
		})

		/**
		 *  Functionality: 三级联动
		 *  targetID: 省份ID 、 城市ID
		 *  flag: p 、 c
		 *  r: 从根目录开始搜索控制器, 不走分组
		 */
		function pcAjax(targetID, flag){
			$.ajax({
				type : "get",
				url  : "/city/pcAjax/?r=&targetID=" + targetID + '&flag=' + flag,
				success:function(data){
					if('p' == flag){
						$('#cityDIV').html(data);
					}else if('c' == flag){
						$('#regionDIV').html(data);
					}
				}
			});
		}
EOF;

		$regionStr = "function cityChange() {
				// 城市查区域
				var selectedCityID = $('#areaCity').val();
				if('' != selectedCityID){
					pcAjax(selectedCityID, 'c');
				}
			}";

		$str .= $regionStr;

		$str .= '</script>';

		//  ======  Step 1: 取得所有的省份, 组成 SELECT, ====== //
		$provinceArr = $this->m_province->getProvinces();

		$provinceSelect = "<span id='provinceDIV'><select eleType='select' id='".$this->provinceID."' name='".$this->provinceID."' class='selecttxt3'>";
		$provinceSelect .= '<option value="0">请选择省份</option>';

		foreach($provinceArr as $val){
			$provinceSelect .= "<option value=".$val['provinceID'];

			if($val['provinceID'] == $PROVINCE){
				$provinceSelect .= " selected = \"selected\" ";
			}

			$provinceSelect .= ">";
			$provinceSelect .= $val['province']."</option>";
		}
		$provinceSelect .= "</select></span>";

		// 城市 DIV
		if($level >= 2) {
			$provinceSelect .= "<span id='cityDIV'></span>";
		}

		// 区域 DIV
		if($level >= 3) {
			$provinceSelect .= "<span id='regionDIV'></span>";
		}

		return $str.$provinceSelect;
	}
	
}