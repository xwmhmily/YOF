<?php
/**
 * 微信类，用于处理微信的一些公用事务
 */

class Weixin {

	private $sdk;

	function __construct(){
		$config = Yaf_Application::app()->getConfig();

		$options = array(
			'token' => $config['wx_token'],
			'appid' => $config['wx_appID'],
			'appsecret' => $config['wx_appSecret'],
		);

		$this->sdk = new Wechat($options);
	}

	/**
	  *  SESSION 中没有 WX 时, 向 WX 官方请求 openID 等信息
	  *  1: 取  code
	  *  2: code 换 token
	  *  3: token 取得 openID 信息, 写入 SESSION
	  */
	public function oauth(){
		if(!$_SESSION['wx']){
			if(isset($_GET['code'])) {
				//第二次进来通过code拿openid 再写入 session['wx']
				$_SESSION['wx'] = $this->_wxCallback();
			} elseif(!isset($_SESSION['user']['openID'])){
				if(ENV == 'DEV'){
					$_SESSION['wx'] = $this->_wxGetOpenIDinUAT();
				} else {
					$callback = SERVER_DOMAIN.$_SERVER['REQUEST_URI'];

					// 只获取基本信息用 snsapi_base [不需要弹出授权页面]
					$url = $this->sdk->getOauthRedirect($callback, '', 'snsapi_base');

					// 获取完全的用户信息则用 snsapi_userinfo [需要弹出授权页面]
					//$url = $this->sdk->getOauthRedirect($callback, '', 'snsapi_userinfo');
					redirect($url);
				}
			}
		}
	}

	/**
	 * 根据callback的code获取openID
	 */
	private function _wxCallback(){
		$tokenArr = $this->sdk->getOauthAccessToken();

		$token = $tokenArr['access_token'];
		$openid = $tokenArr['openid'];

		$wxInfo = $this->sdk->getOauthUserinfo($token, $openid);
		return $this->_wxSaveData($openid, $token, $wxInfo['nickname'], $wxInfo['sex'], $wxInfo['headimgurl']);
	}

	/**
	 * 开发机中不跳转，直接使用默认openID
	 */
	private function _wxGetOpenIDinUAT(){
		$openID = 'devopenid';
		$token  = '';
		$wxName = '微信测试';
		$avatar = '';

		return $this->_wxSaveData($openID, $token, $wxName, 1, $avatar);
	}

	/**
	 * 保存获取到的微信数据
	 *
	 * @param type $openID
	 * @param type $token
	 * @param type $wxName
	 */
	public function _wxSaveData($openID, $token, $wxName, $sex, $avatar = ''){
		$wxName = trim($wxName);
		if(empty($wxName)){
			$wxName = 'TA';
		}

		$wxUser['openID'] = $openID;
		$wxUser['token']  = $token;
		$wxUser['wxName'] = $wxName;
		$wxUser['sex']    = $sex;
		$wxUser['avatar'] = $avatar;

		$m_wxUser = Helper::load('Wx_user');
		$where = array('openID' => $openID);
		$total = $m_wxUser->Where($where)->Total();

		if(!$total){
			$m_wxUser->Insert($wxUser);
		}

		return $wxUser;
	}

}
