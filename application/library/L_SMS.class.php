<?php
/**
 *  File: L_SMS.class.php
 *  Functionality: SMS 发送类
 *  Author: Nic XIE
 *  Date: 2013-8-7
 */

class L_SMS {

	private $username;
	private $password;
	private $url;
	private $mobile;
	private $msg;

	function __construct($mobile = '', $msg = '') {
		$config = Yaf_Application::app()->getConfig();
		$this->username = $config['sms_username'];
		$this->password = $config['sms_password'];
		$this->mobile   = $mobile;
		$this->msg      = $msg;
		$this->url      = $config['sms_url'];
	}


	final function send() {
		$p = '?userId='.$this->username.'&password='.$this->password;
		$p .= '&pszMobis='.$this->mobile.'&pszMsg='.$this->msg.'&iMobiCount=1';

		$this->url .= $p;
		$content = file_get_contents($this->url);
		$result = simplexml_load_string($content);

		if(strlen($result) > 10 && strlen($result) < 25){
			return TRUE;
		}else{
			return FALSE;
		}
	}

}