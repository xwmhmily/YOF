
/**
 *  验证是否为邮箱
 */
function isEmail(m) {
    var email = /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
    return email.test(m);
}

/**
 *  选项卡效果
 */
function SwapTab(name,cls_show,cls_hide,cnt,cur){
    for(i=1;i<=cnt;i++){
        if(i==cur){
            $('#div_'+name+'_'+i).show();
            $('#tab_'+name+'_'+i).attr('class',cls_show);
        }else{
            $('#div_'+name+'_'+i).hide();
            $('#tab_'+name+'_'+i).attr('class',cls_hide);
        }
    }
	
}

/**
 *  全选及反选操作
 */
function selectall(name) {
	$("input[name='"+name+"']").each(function() {
			$(this).attr("checked","checked");
	});
}

/**
 *  验证是否有选中
 */
function checkSelected(name){
    var ids = '';
    $("input[name='"+name+"']:checked").each(function(n){
        ids += $(n).val() + ',';
    });
	
    if(ids == ''){
        return false;
    }else{
        return ids;
    }
}

function cancelAll(){
    var checkboxes=document.getElementsByTagName("INPUT");
    //alert(checkboxes.length);
    var i;
    for(i=0;i<checkboxes.length;i++){
        if(checkboxes[i].type=="checkbox")
            checkboxes[i].checked=false;
    }
}


function fanSelect(){
    var checkboxes=document.getElementsByTagName("INPUT");
    //alert(checkboxes.length);
		
    var i;
    for(i=0;i<checkboxes.length;i++){
        if(checkboxes[i].type=="checkbox"){
            if(checkboxes[i].checked)
                checkboxes[i].checked=false;
            else
                checkboxes[i].checked=true;
        }
    }
}
	
//全选跟取消
function checkAll(){
    var checkboxes=document.getElementsByTagName("INPUT");
    //alert(checkboxes.length);
		
    var i;
    for(i=0;i<checkboxes.length;i++){
        if(checkboxes[i].type=="checkbox"){
            if(checkboxes[i].checked==false)
                checkboxes[i].checked=true;
            else
                checkboxes[i].checked=false;
        }
    }
}
	
//获取指定名称的复选框选中值
function getCheckedVal(checkboxName){
    var checkboxes=document.getElementsByTagName("INPUT");
    var valToString = "";
		
    var i;
    for(i=0;i<checkboxes.length;i++){
        if(checkboxes[i].type=="checkbox" && checkboxes[i].name == checkboxName){
            if(checkboxes[i].checked==true)
                valToString += checkboxes[i].value + ",";
            else
                continue;
        }
    }
    return valToString.substring(0,valToString.length-1);
}
