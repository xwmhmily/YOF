<?php
/**
 * Weixin Controller
 * @package Pet\controller
 */

class WeixinController extends BasicController {

    public function init(){
        // 验证时请注释此行, 验证完成后需要授权则取消该行注释
        //Helper::loadComponment('weixin')->oauth();
    }

    // Verify WX
    public function verifyAction(){
        $echoStr = $this->get('echostr');

        if($this->_checkSinature()){
            echo $echoStr; die;
        }
    }

    private function _checkSinature(){
        $signature = $this->get('signature');
        $timestamp = $this->get('timestamp');
        $nonce = $this->get('nonce');

        $tmpArr = array('your_token_here', $timestamp, $nonce);
        sort($tmpArr);

        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if($tmpStr == $signature){
            return TRUE;
        }else{
            return FALSE;
        }
    }
  	
}
