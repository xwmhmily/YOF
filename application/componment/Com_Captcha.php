<?php
/**
 * File: C_Captcha.php
 * Functionality: text
 * Author: Hao
 * Date: 2013-12-30
 */

class Com_captcha {

	public function __construct() {
		session_start();
		Yaf_Loader::import('SMS');
		Helper::import('String');
	}

	public function sendCaptcha($mobile){
		$rep = array();

		$captcha = getRandom(6, 1);
		$_SESSION['captcha'][$mobile] = $captcha;

		$msg = '您好，您的验证码是' . $captcha;
		$sms = new L_SMS($mobile, $msg);
		$code = $sms->send();
		
		if($code == 0){
			$rep['code'] = 1;
			$rep['error'] = '验证码已发送';
		}else{
			$rep['code'] = 0;
			$rep['error'] = '验证码发送失败';
		}

		return $rep;
	}


	/**
	 * 检查验证码是否正确
	 * 
	 * @param type $mobile
	 * @param type $captcha
	 */
	public function checkCaptcha($mobile, $captcha) {
		$captcha = strtoupper($captcha);
		$sessionCaptcha = strtoupper($_SESSION['captcha'][$mobile]);

		if(!$captcha || $captcha != $sessionCaptcha) {
			return FALSE;
		}

		return TRUE;
	}


	/**
	 * 清除SESSION中的验证码
	 */
	public function clear(){
		unset($_SESSION['captcha'][$mobile]);
    }
}
