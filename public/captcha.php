<?php
/**
 * 验证码生成
 * 
 * Remark: 接收 t 这个参数，可以生成多个验证码
 */
 
session_start();

$t = $_GET['t'];

$num = 4;     //验证码个数
$width = 80;  //验证码宽度
$height = 25; //验证码高度
$code = ' ';

for($i = 0; $i < $num; $i++){//生成验证码
	$code[$i] = chr(rand(65, 90));
}

$_SESSION[$t."Captcha"] = $code;
$image = imagecreate($width, $height);
imagecolorallocate($image, 255, 255, 255);

for($i = 0; $i < 80; $i++) {//生成干扰像素
	$dis_color = imagecolorallocate($image, rand(0, 2555), rand(0, 255), rand(0, 255));
	imagesetpixel($image, rand(1, $width), rand(1, $height), $dis_color);
}

for($i = 0; $i < $num; $i++) {//打印字符到图像
	$char_color = imagecolorallocate($image, 55, 88, 99);
	imagechar($image, 60, ($width / $num) * $i, rand(0, 5), $code[$i], $char_color);
}

header("Content-type:image/png");

imagepng($image);
imagedestroy($image);