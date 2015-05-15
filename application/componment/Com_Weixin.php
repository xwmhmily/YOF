<?php
/**
 * File: Com_weixin.php
 * Functionality:   微信组件，用于处理微信的一些公用事务。
 * @author hao
 * Date: 2014-2-17 16:57:56
 */

class Com_weixin {

	const WX_TOKEN = 'ABCDE';

	private $wxSDK;
	
	public function __construct(){
		$config = Yaf_Application::app()->getConfig();
		Yaf_Loader::import('L_Wechat');

		$options = array(
			'token'        => self::WX_TOKEN,
			'appid'        => $config['wx_appID'],
			'appsecret'    => $config['wx_appsecret'],
			'access_token' => $config['wx_access_token'],
			'expires'      => $config['wx_expires']
		);

		$this->wxSDK = new L_Wechat($options);
	}

	/**
	 * 返回SDK对象
	 */
	public function getSDK(){
		return $this->wxSDK;
	}


	/**
	  *  SESSION 中没有 WX 时, 向 WX 官方请求 openID 等信息
	  *  1: 取  code
	  *  2: code 换 token
	  *  3: token 取得 openID 信息, 写入 SESSION
	  */
	public function oauth(){//开启调试模式，可以在浏览器中打开
		if(!$_SESSION['wx']){
			if(isset($_GET['code'])) {
				//第二次进来通过code拿openid 再写入 session['wx']
				$_SESSION['wx'] = $this->_wxCallback();
			} elseif(!isset($_SESSION['user']['openID'])){
				// UAT 模式下取设定好的
				if(ENVIRONMENT == 'DEV'){
					$_SESSION['wx'] = $this->_wxGetOpenIDinUAT();
				} else {
					$callback = WX_DOMAIN.$_SERVER['REQUEST_URI'];
					$url = $this->wxSDK->getOauthRedirect($callback, '', 'snsapi_base');
					//第一次进来, 取 code
					redirect($url);
				}
			}
		}
	}

	/**
	 * 根据callback的code获取openID
	 */
	private function _wxCallback(){
		$tokenArr = $this->wxSDK->getOauthAccessToken();

		$token = $tokenArr['access_token'];
		$openid = $tokenArr['openid'];

		$wxInfo = $this->wxSDK->getOauthUserinfo($token, $openid);
		return $this->_wxSaveData($openid, $token, $wxInfo['nickname']);
	}

	/**
	 * 开发机中不跳转，直接使用默认openID
	 */
	private function _wxGetOpenIDinUAT(){
		$openID = 'devopenid';
		$token = '';
		$wxName = '微信测试';

		return $this->_wxSaveData($openID, $token, $wxName);
	}

	/**
	 * 保存获取到的微信数据
	 *
	 * @param type $openID
	 * @param type $token
	 * @param type $wxName
	 */
	public function _wxSaveData($openID, $token, $wxName){
		$m_wxUser = $this->load('WxUser');
		$where = array('openID' => $openID);
		$wxUserData = $m_wxUser->Where($where)->SelectOne();

		$code = 0;
		$wxUser = array(
			'openID'   => $openID,
			'wx_token' => $token,
			'wx_name'  => $wxName
		);

		if(!$wxUserData){//如果数据库中没有保存当前微信的openID和token
			$code = $m_wxUser->Insert($wxUser);
		} elseif($token != $wxUserData['wx_token'] || $wxName != $wxUserData['wx_name'] ){//如果token或者微信名和数据库中的不一致，则更新
			$code = $m_wxUser->Where($where)->Update($wxUser);
		} else {//如果数据库中存在微信数据，且内容一致，则直接返回
			$wxUser = $wxUserData;
		}

		return $wxUser;
	}

}
