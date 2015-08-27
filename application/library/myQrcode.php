<?php 
/**
 * File: myQrcode.php
 * Functionality: 二维码类
 * Author: 大眼猫
 * Date: 2013-08-7
 */

Yaf_Loader::import(LIB_PATH.'/qrcode/phpqrcode.php');

class myQrcode {

	public $data;

	/**
	 * $fileName string/bool ：要保存的二维码图片路径，如果不用保存二维码图片则传入false
	 */
	public $fileName;

	/**
	 * $ecc string: 纠错级别：L、M、Q、H  默认为 L
	 */
	private $ecc;

	/**
	 * $size int：二维码 点的大小，1到10,默认为4 手机端扫描。
	 */
	private $size;

	/**
	 * $logo string：网站logo图片路径。
	 */
	private $logo;

	/**
	 * construct 构建方法
	 * @access public
	 * $data string： 二维码各项数据
	 * $fileName string/bool ：要保存的二维码图片路径，如果不用保存二维码图片则传入false
	 * $ecc string: 纠错级别：L、M、Q、H  默认为 L
	 * $size int：二维码 点的大小，1到10,默认为4 手机端扫描。
	 * $logo string：网站logo图片路径， 如果不传生成的二维码图片中间没有网站logo。
	 */
	function __construct($data, $fileName, $ecc, $size, $logo = null){
		$this->data = $data;
		$this->fileName = (!$fileName ? false : $fileName);
		$this->ecc = (!$ecc ? 'L' : $ecc);
		$this->size = (!$size ? 4 : $size);
		$this->logo = $logo;
	}


	/**
	 * Create QRCODE 生成二维码的方法
	 * 
	 * @return false ：该文件存在； true：生成二维码成功。
	 */
	public function createQr(){
		if($this->data) {
			if($this->fileName !== false && file_exists($this->fileName)) {
				return false;
			}
			QRcode::png($this->data, $this->fileName, $this->ecc, $this->size);

			if($this->logo != null){
				$QR = $this->fileName;

				$dd = file_get_contents($QR);
				$QR = imagecreatefromstring(file_get_contents($QR));
				$logo = imagecreatefromstring(file_get_contents($this->logo));
				$QR_width = imagesx($QR);
				$QR_height = imagesy($QR);
				$logo_width = imagesx($logo);
				$logo_height = imagesy($logo);
				$logo_qr_width = $QR_width / 5;
				$scale = $logo_width / $logo_qr_width;
				$logo_qr_height = $logo_height / $scale;
				$from_width = ($QR_width - $logo_qr_width) / 2;
				imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
				imagepng($QR, $this->fileName);
			}
			return true;
		}
	}

}