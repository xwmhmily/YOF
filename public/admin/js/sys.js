/**
 * Created with JetBrains
 * User: alex
 * Date: 13-8-8
 * Time: 下午1:06
 * To change this template use File | Settings | File Templates.
 */

$(document).ready(function(){
    $('#smtp').click(function(){
        if(this.checked){
            $('#smtpcfg').show();
            $('#builtinCfg').hide();
        }
    });

    $('#builtin').click(function(){
        if(this.checked){
            $('#smtpcfg').hide();
            $('#builtinCfg').show();
        }
    });
});
