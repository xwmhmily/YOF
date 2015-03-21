<?php
/**
 * ----------------------------------------------------------------------
 *  File: F_Encryption.php
 *  Functionality: 加密及解密用函数文件
 *  Author: unknow
 *  Date: 2012-11-07
 * ----------------------------------------------------------------------
 */
 
include 'F_String.php';

/**
 * 字符串加密
 * @param string $txt 要加密的字符串
 * @param string $key 自定义key字符串,加密时用到
 * @return string $string 加密后的字符串
 */
function encrypt($txt, $key = 'aJq0q3Yezx-6HdHC8yFEORjlWTgyUHzRzdSgpa4m'){
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_.";
    $ikey = "6acb9732b4a4eb64c01f07f6e80a078f";
    $nh1 = rand(0, 64);
    $nh2 = rand(0, 64);
    $nh3 = rand(0, 64);
    $ch1 = $chars{$nh1};
    $ch2 = $chars{$nh2};
    $ch3 = $chars{$nh3};
    $nhnum = $nh1 + $nh2 + $nh3;
    $knum = 0;
    $i = 0;
    while (isset($key{$i}))
        $knum +=ord($key{$i++});
    $mdKey = substr(md5(md5(md5($key . $ch1) . $ch2 . $ikey) . $ch3), $nhnum % 8, $knum % 8 + 16);
    $txt = base64_encode($txt);
    $txt = str_replace(array('+', '/', '='), array('-', '_', '.'), $txt);
    $tmp = '';
    $j = 0;
    $k = 0;
    $tlen = strlen($txt);
    $klen = strlen($mdKey);

    for ($i = 0; $i < $tlen; $i++) {
        $k = $k == $klen ? 0 : $k;
        $j = ($nhnum + strpos($chars, $txt{$i}) + ord($mdKey{$k++})) % 64;
        $tmp .= $chars{$j};
    }

    $tmplen = strlen($tmp);
    $tmp = substr_replace($tmp, $ch3, $nh2 % ++$tmplen, 0);
    $tmp = substr_replace($tmp, $ch2, $nh1 % ++$tmplen, 0);
    $tmp = substr_replace($tmp, $ch1, $knum % ++$tmplen, 0);

    // add 
    $tmp = getRandom(5, 5) . $tmp . getRandom(8, 5);
    $tmp = strrev($tmp);

    return $tmp;
}

/**
 * 字符串解密
 * @param string $txt 要解密的字符串
 * @param string $key 自定义key字符串,解密时用到,与加密函数定义的一样
 * @return string $string 解密后的原始字符串
 */
function decrypt($txt,  $key = 'aJq0q3Yezx-6HdHC8yFEORjlWTgyUHzRzdSgpa4m'){
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_.";
    $ikey = "6acb9732b4a4eb64c01f07f6e80a078f";
    $knum = 0;
    $i = 0;

    // add
    $txt = strrev($txt);
    $txt = substr(substr($txt, 0, -8), 5);

    $tlen = strlen($txt);
    while (isset($key{$i}))
        $knum +=ord($key{$i++});
    $ch1 = $txt{$knum % $tlen};
    $nh1 = strpos($chars, $ch1);
    $txt = substr_replace($txt, '', $knum % $tlen--, 1);
    $ch2 = $txt{$nh1 % $tlen};
    $nh2 = strpos($chars, $ch2);
    $txt = substr_replace($txt, '', $nh1 % $tlen--, 1);
    $ch3 = $txt{$nh2 % $tlen};
    $nh3 = strpos($chars, $ch3);
    $txt = substr_replace($txt, '', $nh2 % $tlen--, 1);
    $nhnum = $nh1 + $nh2 + $nh3;
    $mdKey = substr(md5(md5(md5($key . $ch1) . $ch2 . $ikey) . $ch3), $nhnum % 8, $knum % 8 + 16);
    $tmp = '';
    $j = 0;
    $k = 0;
    $tlen = strlen($txt);
    $klen = strlen($mdKey);

    for ($i = 0; $i < $tlen; $i++) {
        $k = $k == $klen ? 0 : $k;
        $j = strpos($chars, $txt{$i}) - $nhnum - ord($mdKey{$k++});
        while ($j < 0)
            $j+=64;
        $tmp .= $chars{$j};
    }

    $tmp = str_replace(array('-', '_', '.'), array('+', '/', '='), $tmp);
    return base64_decode($tmp);
}