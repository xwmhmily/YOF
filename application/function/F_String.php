<?php
/**
 * File: F_String.php
 * Functionality: Extra string functions
 * Author: Nic XIE
 * Date: 2012-3-5
 */

// Generate specific lenght random chars or numbers, or both
function getRandom($length = 4, $type = 1) {
    switch ($type) {
        case 1:
            $string = '1234567890';
        break;
		
        case 2:
            $string = 'abcdefghijklmnopqrstuvwxyz';
        break;
		
        case 3:
            $string = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        break;
		
        case 4:
            $string = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        break;
		
        case 5:
            $string = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        break;
    }
    $output = '';
    for ($i = 0; $i < $length; $i++) {
        $pos = mt_rand(0, strlen($string) - 1); 
        $output .= $string[$pos];
    }
    return $output;
}

// Convert a string to an array
function stringToArray($string) {
    $length = strlen($string);

    $arr = array();
    for ($i = 0; $i < $length; $i++) {
        $arr[] = $string[$i];
    }

    return $arr;
}


/**
 * 字符串转数组
 * @param string $tags 标签字符串
 * @return array $array 数组数组
 */
function string2array($tags) {
    return preg_split('/\s*,\s*/', trim($tags), -1, PREG_SPLIT_NO_EMPTY);
}


/**
 * 数组转字符串
 * @param array $tags 标签数组
 * @return string $string 标签字符串
 */
function array2string($tags) {
    return implode(',', $tags);
}

/**
 * 把手机号的中间四位换成*号
 */
function convertMobile($mobile) {
	$pattern = "/(1\d{1,2})\d\d(\d{0,3})/";
	$replacement = "\$1****\$3";

	$mobile = preg_replace($pattern, $replacement, $mobile);
	return $mobile;
}


/**************************************************************
*
*  使用特定function对数组中所有元素做处理
*  @param  string  &$array     要处理的字符串
*  @param  string  $function   要执行的函数
*  @return boolean $apply_to_keys_also     是否也应用到key上
*  @access public
*
*************************************************************/
function arrayRecursive(&$array, $function, $apply_to_keys_also = false){
	static $recursive_counter = 0;
	if (++$recursive_counter > 1000) {
		die('possible deep recursion attack');
	}
	
	if($array){
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				arrayRecursive($array[$key], $function, $apply_to_keys_also);
			} else {
				$array[$key] = $function($value);
			}
	      
			if ($apply_to_keys_also && is_string($key)) {
				$new_key = $function($key);
				if ($new_key != $key) {
					$array[$new_key] = $array[$key];
					unset($array[$key]);
				}
			}
	    }
	}
    $recursive_counter--;
}
      
/**************************************************************
*
*  将数组转换为JSON字符串（兼容中文）
*  @param  array   $array      要转换的数组
*  @return string      转换得到的json字符串
*  @access public
*
*************************************************************/
function JSON($array) {
	arrayRecursive($array, 'urlencode', TRUE);
	$json = json_encode($array);
	return urldecode($json);
}


/***********************************************************
 * 截取中文字符串
 **********************************************************/
function subString_UTF8($str, $start, $lenth)
{
	$len = strlen($str);
	$r = array();
	$n = 0;
	$m = 0;
	for($i = 0; $i < $len; $i++) {
		$x = substr($str, $i, 1);
		$a  = base_convert(ord($x), 10, 2);
		$a = substr('00000000'.$a, -8);
		if ($n < $start){
			if (substr($a, 0, 1) == 0) {
			}elseif (substr($a, 0, 3) == 110) {
				$i += 1;
			}elseif (substr($a, 0, 4) == 1110) {
				$i += 2;
			}
			$n++;
		}else{
			if (substr($a, 0, 1) == 0) {
				$r[ ] = substr($str, $i, 1);
			}elseif (substr($a, 0, 3) == 110) {
				$r[ ] = substr($str, $i, 2);
				$i += 1;
			}elseif (substr($a, 0, 4) == 1110) {
				$r[ ] = substr($str, $i, 3);
				$i += 2;
			}else{
				$r[ ] = '';
			}
			if (++$m >= $lenth){
				break;
			}
		}
	}
	return $r;
}

/**
 * [cn_substr_utf8 网上下载的PHP 截取中文字符函数]
 * @param  string  $str    [被截取的字符串]
 * @param  integer $length [长度]
 * @param  integer $start  [开始的地方]
 * @author [yun] <[email]>
 * @return [type]          [description]
 */
function cn_substr_utf8($str = "", $start = 0, $length = 7) {
	$lgocl_str = $str;
	if (strlen($str) < $start + 1) {
		
		return '';
	}

	preg_match_all("/./su", $str, $ar);

	$str = '';
	$tstr = '';

	//为了兼容mysql4.1以下版本,与数据库varchar一致,这里使用按字节截取
	
	for ($i = 0; isset($ar[0][$i]); $i++) {
		if (strlen($tstr) < $start) {
			$tstr.= $ar[0][$i];
		} else {
			if (strlen($str) < $length + strlen($ar[0][$i])) {
				$str.= $ar[0][$i];
			} else {
				break;
			}
		}
	}

	if (strlen($lgocl_str) <= $length) {
	} else {
		$str.= "...";
	}
	
	return $str;
}

/**
 * 处理wp options 函数
 */
function maybe_unserialize( $original ) {
	if ( is_serialized( $original ) ) // don't attempt to unserialize data that wasn't serialized going in
		return @unserialize( $original );
	return $original;
}

function is_serialized( $data, $strict = true ) {
	// if it isn't a string, it isn't serialized
	if ( ! is_string( $data ) )
		return false;
	$data = trim( $data );
 	if ( 'N;' == $data )
		return true;
	$length = strlen( $data );
	if ( $length < 4 )
		return false;
	if ( ':' !== $data[1] )
		return false;
	if ( $strict ) {
		$lastc = $data[ $length - 1 ];
		if ( ';' !== $lastc && '}' !== $lastc )
			return false;
	} else {
		$semicolon = strpos( $data, ';' );
		$brace     = strpos( $data, '}' );
		// Either ; or } must exist.
		if ( false === $semicolon && false === $brace )
			return false;
		// But neither must be in the first X characters.
		if ( false !== $semicolon && $semicolon < 3 )
			return false;
		if ( false !== $brace && $brace < 4 )
			return false;
	}
	$token = $data[0];
	switch ( $token ) {
		case 's' :
			if ( $strict ) {
				if ( '"' !== $data[ $length - 2 ] )
					return false;
			} elseif ( false === strpos( $data, '"' ) ) {
				return false;
			}
			// or else fall through
		case 'a' :
		case 'O' :
			return (bool) preg_match( "/^{$token}:[0-9]+:/s", $data );
		case 'b' :
		case 'i' :
		case 'd' :
			$end = $strict ? '$' : '';
			return (bool) preg_match( "/^{$token}:[0-9.E-]+;$end/", $data );
	}
	return false;
}
