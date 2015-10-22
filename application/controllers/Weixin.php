<?php
/**
 * Weixin Controller
 */

define('TOKEN', 'your_token_here');

class WeixinController extends BasicController {

    public function init(){
        session_start();
        
        /* 
         * 验证时请注释此行, 验证完成后需要授权则取消该行注释
         * 拿到用户的微信信息后会保存在 $_SESSION['wx'] 里
         */
        //$l_weixin = new Weixin();
        //$l_weixin->oauth();
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

        $tmpArr = array(TOKEN, $timestamp, $nonce);
        sort($tmpArr);

        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if($tmpStr == $signature){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    // 生成分享需要的数据
    // @param string $url : 要分享的URL
    private function _generateShare($url){
        $config    = Yaf_Application::app()->getConfig();
        $appID     = $config['wx_appID'];
        $appSecret = $config['wx_appSecret'];

        $share['appID']     = $appID;
        $share['timestamp'] = CUR_TIMESTAMP;
        $share['nonceStr']  = 'Wu5WZYThz1wzccnX';
        $share['link']      = $url;

        // 验证 ticket 是否失效
        $m_ticket = $this->load('Ticket');
        $ticket   = $m_ticket->SelectOne(); 

        $gap = CUR_TIMESTAMP - $ticket['addTime'];

        if (!$ticket['ticket'] || $gap > 7200){
            // Get token
            $token = $this->_getToken($appID, $appSecret);

            // Get ticket
            $ticket = $this->_getTicket($token);

            if($ticket){
                $jsapi_ticket = $m['ticket'] = $ticket;
                $m['addTime'] = CUR_TIMESTAMP;

                $m_ticket->Where(1)->Delete();
                $m_ticket->Insert($m);
            }
        }else{
            $jsapi_ticket = $ticket['ticket'];
        }

        // Get signature
        $string1 = 'jsapi_ticket='.$jsapi_ticket.'&noncestr='.$share['nonceStr'];
        $string1 .= '&timestamp='.$share['timestamp'].'&url='.$url;

        $share['signature'] = sha1($string1);

        return $share;
    }

    // Get token
    private function _getToken($appID, $appSecret){
        $token_url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appID.'&secret='.$appSecret;
        $result = file_get_contents($token_url);

        $result = json_decode($result, TRUE);
        return $result['access_token'];
    }

    // Get ticket for weixin share
    private function _getTicket($token){
        $ticket_url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$token.'&type=jsapi';
        $result = file_get_contents($ticket_url);
        $result = json_decode($result, TRUE);

        return $result['ticket'];
    }
  	
    public function indexAction(){
        // 需要 Weixin 分享则调用
        $url = $url = "http://".$_SERVER['HTTP_HOST'] .$_SERVER['REQUEST_URI'];
        $buffer['share'] = $this->_generateShare($url);

        // 点击分享链接进来后的 URL 
        $buffer['targetURL'] = SERVER_DOMAIN.'/weixin/share';
        
        echo 'Here is your code ...'; die;
    }
}
