<?php
/**
 * File: F_Validate.php
 * Functionality: Extra validate functions
 * Author: Nic XIE
 * Date: 2012-03-01
 */

// Check var is a valid email or not
function isEmail($email) {
	if (!$email) {
		return false;
	}

	return preg_match('/^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,4}$/', $email);
}

// Check var is a valid Chinese mobile or not
function isMobile($mobile) {
	if (!$mobile) {
		return false;
	}

	return preg_match('/^((\(d{2,3}\))|(\d{3}\-))?1(3|5|8|9)\d{9}$/', $mobile);
}

// Check var is a valid postal code or not
function isPostalCode($postalCode) {
	if (!$postalCode) {
		return false;
	}

	return preg_match("/^[1-9]\d{5}$/", $postalCode);
}

// Check var is a valid IP Address or not
function isIPAddress($IPAddress) {
	if (!$IPAddress) {
		return false;
	}

	return preg_match("/^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])" .
                    "(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}$/", $IPAddress);
}

// Check var is a valid ID card or not
function isIDCard($IDCard) {
	if (!$IDCard) {
		return false;
	}

	return preg_match('/(^([\d]{15}|[\d]{18}|[\d]{17}x)$)/', $IDCard);
}

/**
 * 检查中文
 * @param string $str 标签字符串
 */
function isCn($str){
	if(preg_match("/[\x{4e00}-\x{9fa5}]+/u", $str)) {
		return true;
	}
	return false;
}

/**
 * 检查数字
 * @param string $str 标签字符串
 */
function isNumber($str){
	if(preg_match('/^\d+$/', $str)) {
		return true;
	}
	return false;
}

/**
 * 检查是否每位相同
 * @param string $str 标签字符串
 */
function isNumSame($str){
	if(preg_match('/^(\w)\1+$/', $str)) {
		return true;
	}
	return false;
}

/**
 * 检查是否为空
 * @param string $str 标签字符串
 */
function isEmpty($str){
	//$str = trim($str);
	if(preg_match('/^\s*$/', $str)) {
		return true;
	}
	return false;
}


/**
 * 检测是否为合法url
 */
function isUrl($url){
	if(strpos('kkk' . $url, 'http')){
		return true;
	}
	return false;
}


// 检测一组字符是否有可能组成手机号码
function willMobile($mobile) {
	if (!$mobile) {
		return false;
	}

	return preg_match('/^((\(d{2,3}\))|(\d{3}\-))?1(3|5|8|9)\d{0,9}$/', $mobile);
}

function isPhoneNumber($phone) {
	if (!$phone) {
		return false;
	}
echo($phone);
	return preg_match('/^((0\d{3}[\-])?\d{7}|(0\d{2}[\-])?\d{8})?$/', $phone);
}

function isAreaCode($code){
	if (!$code) {
		return false;
	}

	return preg_match('/^(0\d{3})|(0\d{2})$/', $code);
}