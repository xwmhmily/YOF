<?php
/**
 * File: Com_Html.php
 * Functionality: 构建 HTML 组件
 * Author: Nic XIE
 * Date: 2014-5-14
 */

class Com_Html {
	
	/*
      *  构建 HTML SELECT 组件
	  *  @param array $arr
	  *  @param int $id : SELECT 中的 ID
	  *  @param int $selectID : 被选中的 ID
	  *  @param int $onchange 是否需要 onChange 事件, 0=>不需要, 1=>需要
	  *  @return SELECT 组件
	  *  @remark: 
			1 => $arr 中的 key 为value, val 则显示
			2 => 不会自动带上 onchange 事件
	  */
	public function buildSelect($arr, $id, $selectID = '', $onchange = 0){
		$html = '<select id="'.$id.'" name="'.$id.'"';
		
		if($onchange){
			$html .= ' onchange="javascript:'.$id.'Change();"';
		}
		
		$html .= '>'. NL;
		
		foreach($arr as $key => $val){
			$html .= '<option value="'.$key.'"';
			if($selectID && $selectID == $key){
				$html .= " selected=\"selected\" ";
			}
			$html .= '>'.$val.'</option>'. NL;
		}
		$html .= '</select>';
		
		return $html;
	}
	
	/*
      *  构建 HTML 第二种 SELECT 组件
	  *  @param array $arr
	  *  @param int $id : SELECT 中的 ID
	  *  @param int $selectID : 被选中的 ID
	  *  @param int $onchange 是否需要 onChange 事件, 0=>不需要, 1=>需要
	  *  @return SELECT 组件
	  *  @remark: build 出来的 select 的 value 和 显示都是 $val
	  */
	public function buildYetSelect($arr, $id, $selectID = '', $onchange = 0){
		$html = '<select id="'.$id.'" name="'.$id.'" eleType="select"';
		
		if($onchange){
			$html .= ' onchange="javascript:'.$id.'Change();"';
		}
		
		$html .= '>';
		foreach($arr as $key => $val){
			$html .= '<option value="'.$val.'"';
			if($selectID && $selectID == $val){
				$html .= " selected=\"selected\" ";
			}
			$html .= '>'.$val.'</option>';
		}
		$html .= '</select>';
		
		return $html;
	}
	
	
	/*
      *  构建 HTML LI 形式的SELECT 组件
	  *  @param array $arr
	  *  @param int $id : SELECT 中的 ID
	  *  @param int $selectID : 被选中的 ID
	  *  @return SELECT 组件
	  *  @remark: 
			1 => $arr 中 val['label'] 为显示名称, val['url'] 为跳转 URL
	  */
	public function buildLiSelect($arr, $id, $selectID = ''){
		if(!$selectID) $selectID = 0;
		
		$html = '<span>'.$arr[$selectID]['label'].'</span><div class="toselect"><ul>';
		foreach($arr as $key => $val){
			$html .= '<li><a href="'.$val['url'].'">'.$val['label'].'</a></li>';
		}
		$html .= '</ul></div>';
		
		return $html;
	}
	
}