<?php
/**
 * File: F_Charset.php
 * Functionality: Extra charset functions
 * Author: Nic XIE
 * Date: 2011-11-20
 */

// Convert charset encoding
function converCharset($from, $to, $content){
	if(empty($from) || empty($to) || empty($content)) return '';
	if(function_exists('iconv')){
		$content = iconv($from, $to, $content);
	}else if(function_exists('mb_convert_encoding')){
		$content = mb_convert_encoding($content, $to, $from);
	}
	return $content;
}