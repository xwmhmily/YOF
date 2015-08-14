<?php
/**
 * Weixin Controller
 */

class WeixinController extends BasicController {

    public function init(){
        session_start();
        
        /* 
         * 验证时请注释此行, 验证完成后需要授权则取消该行注释
         * 拿到用户的微信信息后会保存在 $_SESSION['wx'] 里
         */
        //Helper::loadComponment('weixin')->oauth();
    }

    // Verify
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
  	
    public function indexAction(){
        echo 'Here is your code ...'; die;
    }
}
