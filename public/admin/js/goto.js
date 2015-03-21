/* 	File: goto.js
 *  Functionality: 此 JS 用于用户在分页导航栏输入页数跳至指定的页
 *	Author: Nic XIE
 *  Date: 2012-10-30
 *  Remark: 需要此功能的请在模板页中引入此 JS
 */
$(document).ready(function(){
	
	$('#txtGoto').keyup(function(){
		var page = $('#txtGoto').val();
		var totalPages = parseInt($('#totalPages').html());
		
		if(isNaN(page) || page == 0 || page > totalPages){
			$('#txtGoto').val('');
		}
	});
	
	$('#btnGoto').click(function(){
		var page = $('#txtGoto').val();
		if(page == ""){
			$('#txtGoto').focus();
			return false;
		}
		
		var url = $('#self_url').val();
		url += url.indexOf("?") > 0 ? "&" : "?";
		url += 'page=' + $('#txtGoto').val();
		window.location.href = url;
	});
	
});
