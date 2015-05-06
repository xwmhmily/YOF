/*
 *	File: privilege.js
 *  Functionality: 权限控制 JS
 *  Author: Nic XIE
 *  Date: 2013-03-20
 */

// 选择与反选子菜单
function selectChild(parentID){
	//var chk = $('#div_'+parentID+' name = sub_'+parentID+'[]');
	var chk = $("#div_" + parentID + " input[type='checkbox']");
	if($('#parent_'+parentID).attr('checked') == true){
		chk.each(function(){
			$(this).attr('checked',true);
		});
	}else{
		chk.attr('checked', false);
	}
}

/* 
 *  Function: checkParent
 *  Functionality: 复选子菜单, 判断父菜单及依赖关系
 *  1: 选中本身, 要选中父菜单, 选中依赖项
 *  2: 取消选中本身
		A: 取消依赖此项的子项
		B: 判断兄弟 checkbox 是否均被取消了, 如果是, 取消选中父菜单
 *  @Params:
		1: childID  => 子菜单ID [本身ID]
		2: parentID => 父菜单ID
		3: dep      => 依赖码. 如团购管理中的 删除[204] 依赖 200
 */
function checkParent(childID, parentID, dep){
	if($('#subItem_'+childID).attr('checked') == true){
		$('#parent_'+parentID).attr('checked', true);
		if(dep != ''){
			$('#subItem_'+dep).attr('checked', true);
		}
	}else{
		// 1: 取消依赖此项的子项
		if(dep == ''){
			$('#div_'+parentID).find('input[type="checkbox"]').each(function(){
				var titleAttr = $(this).attr('title');
				var titleInfo = titleAttr.split('_');
				var title = titleInfo[0];
				var subID = titleInfo[1];
				if(title == childID){
					$('#subItem_'+subID).attr('checked', false);
					i++;
				}
			});
		}

		// 2: 查找兄弟节点是否全没全中
		var i = 0;
		var count = $('#div_'+parentID).find('input[type="checkbox"]').length;
		
		$('#div_'+parentID).find('input[type="checkbox"]').each(function(){
			if($(this).attr('checked') == false){
				i++;
			}
		});
		
		if(count == i){
			$('#parent_'+parentID).attr('checked', false);
		}
	}
}